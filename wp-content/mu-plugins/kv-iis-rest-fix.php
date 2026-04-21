<?php
/**
 * Plugin Name: KV IIS REST API Auth Fix
 * Description: Fixes WordPress REST API authentication on IIS servers where cookie parsing fails.
 * Version: 1.0
 *
 * This MU plugin runs BEFORE themes and regular plugins.
 * It fixes IIS-specific issues with auth cookie handling for REST API requests.
 */

// === Step 1: Fix IIS URL-encoded cookies ===
// IIS may URL-encode pipe characters (|) in cookies, breaking WordPress cookie parsing.
// This runs at the earliest possible point (before WordPress parses cookies).
foreach ($_COOKIE as $name => $value) {
    if (strpos($name, 'wordpress_') === 0 && is_string($value)) {
        if (strpos($value, '%7C') !== false) {
            $_COOKIE[$name] = urldecode($value);
        }
    }
}

// === Step 1.5: Capture stray output before REST API JSON ===
$is_rest = (
    (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false) ||
    (isset($_GET['rest_route']))
);
if ($is_rest) {
    ob_start();
    add_filter('rest_pre_serve_request', function ($served, $result, $request, $server) {
        $buffered = ob_get_clean();
        if (!empty(trim($buffered))) {
            $log = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/kv-mu-auth.log' : '/tmp/kv-mu-auth.log';
            @error_log(sprintf(
                "[%s] STRAY_OUTPUT: len=%d content=%s uri=%s\n",
                gmdate('c'),
                strlen($buffered),
                substr(str_replace(["\n", "\r"], ' ', $buffered), 0, 500),
                isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''
            ), 3, $log);
        }
        return $served;
    }, 1, 4);
}

add_action('rest_api_init', function () {
    $log = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/kv-mu-auth.log' : '/tmp/kv-mu-auth.log';
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

    $wp_cookie_names = [];
    $logged_in_value_preview = 'none';
    foreach ($_COOKIE as $name => $value) {
        if (strpos($name, 'wordpress') === 0 || strpos($name, 'wp-') === 0) {
            $wp_cookie_names[] = $name;
            if (strpos($name, 'wordpress_logged_in_') === 0) {
                $logged_in_value_preview = substr($value, 0, 30) . '...(len=' . strlen($value) . ',pipes=' . substr_count($value, '|') . ')';
            }
        }
    }

    $uid = get_current_user_id();
    @error_log(sprintf(
        "[%s] MU_REST_INIT: uri=%s uid=%d logged_in=%s cookies=[%s] logged_in_cookie=%s\n",
        gmdate('c'),
        $uri,
        $uid,
        is_user_logged_in() ? 'yes' : 'no',
        implode(', ', $wp_cookie_names),
        $logged_in_value_preview
    ), 3, $log);

    remove_filter('rest_authentication_errors', 'rest_cookie_check_errors', 100);

    add_filter('rest_authentication_errors', function ($result) use ($log) {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        if (!empty($result)) {
            @error_log(sprintf("[%s] MU_AUTH: skipping, result not empty: %s uri=%s\n",
                gmdate('c'),
                is_wp_error($result) ? 'WP_Error(' . $result->get_error_code() . ')' : gettype($result),
                $uri
            ), 3, $log);
            return $result;
        }

        if (is_user_logged_in()) {
            $uid = get_current_user_id();
            @error_log(sprintf("[%s] MU_AUTH: already logged in uid=%d, ALLOWING uri=%s\n",
                gmdate('c'), $uid, $uri
            ), 3, $log);
            rest_get_server()->send_header('X-WP-Nonce', wp_create_nonce('wp_rest'));
            return true;
        }

        $cookie_value = '';
        $cookie_name_found = '';
        foreach ($_COOKIE as $name => $value) {
            if (strpos($name, 'wordpress_logged_in_') === 0) {
                $cookie_value = $value;
                $cookie_name_found = $name;
                break;
            }
        }

        if (empty($cookie_value)) {
            @error_log(sprintf("[%s] MU_AUTH: no logged_in cookie found, anonymous request uri=%s\n",
                gmdate('c'), $uri
            ), 3, $log);
            return $result;
        }

        @error_log(sprintf("[%s] MU_AUTH: found cookie '%s' len=%d pipes=%d uri=%s\n",
            gmdate('c'), $cookie_name_found, strlen($cookie_value), substr_count($cookie_value, '|'), $uri
        ), 3, $log);

        $user_id = wp_validate_auth_cookie($cookie_value, 'logged_in');
        if ($user_id) {
            wp_set_current_user($user_id);
            @error_log(sprintf("[%s] MU_AUTH: wp_validate_auth_cookie OK uid=%d, ALLOWING\n",
                gmdate('c'), $user_id
            ), 3, $log);
            rest_get_server()->send_header('X-WP-Nonce', wp_create_nonce('wp_rest'));
            return true;
        }

        @error_log(sprintf("[%s] MU_AUTH: wp_validate_auth_cookie FAILED, trying manual parse\n",
            gmdate('c')
        ), 3, $log);

        $try_values = [$cookie_value];
        if (strpos($cookie_value, '%') !== false) {
            $try_values[] = urldecode($cookie_value);
        }
        if (strpos($cookie_value, '+') !== false) {
            $try_values[] = str_replace('+', ' ', $cookie_value);
        }

        foreach ($try_values as $try_value) {
            $elements = explode('|', $try_value);
            if (count($elements) !== 4) {
                continue;
            }

            list($username, $expiration, $token, $hmac) = $elements;

            if ((int) $expiration < time()) {
                continue;
            }

            $user = get_user_by('login', $username);
            if (!$user) {
                continue;
            }

            $pass_frag = substr($user->user_pass, 8, 4);
            $key = wp_hash($username . '|' . $pass_frag . '|' . $expiration . '|' . $token, 'logged_in');
            $algo = function_exists('hash') ? 'sha256' : 'sha1';
            $hash = hash_hmac($algo, $username . '|' . $expiration . '|' . $token, $key);

            if (!hash_equals($hash, $hmac)) {
                @error_log(sprintf("[%s] MU_AUTH: HMAC mismatch for user '%s'\n",
                    gmdate('c'), $username
                ), 3, $log);
                continue;
            }

            $manager = WP_Session_Tokens::get_instance($user->ID);
            if (!$manager->verify($token)) {
                @error_log(sprintf("[%s] MU_AUTH: session token invalid for user '%s'\n",
                    gmdate('c'), $username
                ), 3, $log);
                continue;
            }

            wp_set_current_user($user->ID);
            @error_log(sprintf("[%s] MU_AUTH: manual parse OK uid=%d user=%s, ALLOWING\n",
                gmdate('c'), $user->ID, $username
            ), 3, $log);
            rest_get_server()->send_header('X-WP-Nonce', wp_create_nonce('wp_rest'));
            return true;
        }

        @error_log(sprintf("[%s] MU_AUTH: all parsing attempts failed uri=%s\n",
            gmdate('c'), $uri
        ), 3, $log);
        return $result;
    }, 100);

    add_filter('rest_pre_serve_request', function ($served, $result, $request, $server) {
        $method = $request->get_method();
        $is_write = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true);
        $is_edit = ($request->get_param('context') === 'edit');
        if (!$is_write && !$is_edit) return $served;

        $log = WP_CONTENT_DIR . '/kv-mu-auth.log';
        $route = $request->get_route();
        $status = $result->get_status();

        $ob_content = '';
        if (ob_get_level() > 0) {
            $ob_content = ob_get_contents();
        }

        $headers_sent = headers_sent($file, $line);

        @error_log(sprintf(
            "[%s] REST_RESPONSE: method=%s route=%s status=%d ob_level=%d ob_len=%d headers_sent=%s(%s:%d) uri=%s\n",
            gmdate('c'), $method, $route, $status, ob_get_level(),
            strlen($ob_content), $headers_sent ? 'yes' : 'no',
            $headers_sent ? $file : '', $headers_sent ? $line : 0,
            isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''
        ), 3, $log);

        if (!empty(trim($ob_content))) {
            @error_log(sprintf(
                "[%s] REST_RESPONSE_OB: %s\n",
                gmdate('c'),
                substr(str_replace(["\n", "\r"], ' ', $ob_content), 0, 1000)
            ), 3, $log);
        }

        return $served;
    }, 0, 4);
}, 0);
