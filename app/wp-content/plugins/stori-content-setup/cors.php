<?php 
defined('ABSPATH') or die('No script kiddies please!');

function stori_init_cors($value) {
    $origin_url = '*';
  
    // Check if production environment or not
    if (WP_ENVIRONMENT_TYPE === 'production') {
        $origin_url = (defined('HEADLESS_MODE_CLIENT_URL') ? HEADLESS_MODE_CLIENT_URL : esc_url_raw(site_url()));
    }
  
    header('Access-Control-Allow-Origin: ' . $origin_url);
    header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,PATCH');
    header('Access-Control-Allow-Credentials: true');
    return $value;
}  

function stori_add_cors() {
	remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter( 'rest_pre_serve_request', 'stori_init_cors');
}

add_action('rest_api_init', 'stori_add_cors');

