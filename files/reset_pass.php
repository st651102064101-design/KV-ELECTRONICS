<?php
require_once dirname(__FILE__) . '/wp-load.php';

$username = 'root';
$password = ')Nk6u#rpM3tqgsFn()$*r*yr';
$email = 'admin@example.com';

$user = get_user_by('login', $username);

if ($user) {
    wp_set_password($password, $user->ID);
    echo "SUCCESS: Password for '$username' has been reset.\n";
} else {
    $user_id = wp_create_user($username, $password, $email);
    if (!is_wp_error($user_id)) {
        $user = new WP_User($user_id);
        $user->set_role('administrator');
        echo "SUCCESS: Admin user '$username' has been created with the provided password.\n";
    } else {
        echo "ERROR: " . $user_id->get_error_message() . "\n";
    }
}
