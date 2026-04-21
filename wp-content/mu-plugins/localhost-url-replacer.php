<?php
/**
 * Plugin Name: KV Electronics Localhost URL Replacer
 * Description: Dynamically replaces production URLs with localhost URLs so images and links work locally without destroying the production database.
 */

if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    add_action('template_redirect', function() {
        ob_start(function($output) {
            $prod_url = 'https://kv-electronics.com';
            $prod_url_files = 'https://kv-electronics.com/files';
            
            $local_url = 'http://localhost/KV-ELECTRONICS/files';
            
            // First safely replace the /files version if it exists
            $output = str_replace($prod_url_files, $local_url, $output);
            $output = str_replace(str_replace('/', '\/', $prod_url_files), str_replace('/', '\/', $local_url), $output);
            
            // Then replace the root version
            $output = str_replace($prod_url, $local_url, $output);
            $output = str_replace(str_replace('/', '\/', $prod_url), str_replace('/', '\/', $local_url), $output);
            
            return $output;
        });
    });
}
