<?php 
defined('ABSPATH') or die('No script kiddies please!');
define('MY_ACF_PATH', dirname(__FILE__) . '/inc/acf/');
define('MY_ACF_URL', plugin_dir_url(__FILE__) . '/inc/acf/');

include_once(dirname(__FILE__) . '/inc/plural.php');
include_once(dirname(__FILE__) . '/fix_urls.php');
include_once(dirname(__FILE__) . '/cors.php');
include_once(dirname(__FILE__) . '/register_types.php');
include_once(dirname(__FILE__) . '/graphql.php');
include_once(dirname(__FILE__) . '/response_sanitizer.php');
include_once(MY_ACF_PATH . 'acf.php');
include_once(dirname(__FILE__) . '/publish_to_netlify.php');
new PublishToNetlifyHook();

function my_acf_settings_url( $url ) {
    return MY_ACF_URL;
}

add_filter('acf/settings/url', 'my_acf_settings_url');
add_filter('acf/settings/show_admin', '__return_false');
add_filter('acf/settings/show_updates', '__return_false', 100);


/** 
* Plugin Name:       Stori Content Setup
* Plugin URI:        
* Description:       Add Content Types in Admin UI
* Version:           1.0.0
* Author:            Patreo
* Author URI:        http://patreo.com
* License:           MIT
* License URI:       
* Text Domain:       stori-content-setup
*/

/**
 * Create Stori menu
 *
 * @return void
 */
function stori_admin_menu() {
     add_menu_page('Stori', 'Stori', 'manage_options', 'stori', 'stori_page_init', 'dashicons-dashboard', 2);
     add_submenu_page('stori', 'Content Setup', 'Content Setup', 'manage_options', 'edit.php?post_type=stori_content_type');
     add_submenu_page('stori', 'Fields', 'Fields', 'manage_options', 'edit.php?post_type=acf-field-group');
     add_submenu_page('stori', 'Templates', 'Templates', 'manage_options', 'edit.php?post_type=stori_template');	 
     remove_menu_page('edit.php?post_type=acf-field-group'); 
}

/**
 * Create entry page
 *
 * @return void
 */
function stori_page_init() {
?>
	 <h1>About Stori.</h1>
	 <p>In simple terms, the plugin removes the frontend of the WordPress site. Post permalinks go straight to the editor page and the theme is mostly redundant.</p>

	 <h2>Usage</h2>
	 <p><code>/api/wp/v2/[custom_post_type]</code> or <code>/api/wp/v2/users</code> or <code>/api/wp/v2/media</code> or <code>/api/wp/v2/taxonomies</code> or <code>/api/wp/v2/tags</code> or <code>/api/wp/v2/categories</code> or <code>/api/wp/v2/stori_template</code><p>

	 <h2>Disabled Endpoints</h2>
	 <p>By default <code>Posts</code>, <code>Pages</code>, <code>Comments</code>, <code>Settings</code>, <code>Themes</code>, <code>Plugins</code>, <code>Search</code> and <code>Blocks</code> because is not in agreement with objectives of this plugin.</p>

	 <h2>Authorization</h2>
	 <p>Use <code>/api/jwt-auth/v1/token</code> submiting a POST request with parameters <code>username</code> and <code>password</code>, this will return a response with user information and a header with JWT token authorization.<br /><strong>Note: </strong>To secure your application don't use own password, please use <a href="/wp-admin/profile.php">application password</a> instead.</p>

	 <h2>Dependencies</h2>
	 <ul>
		<li>Classic Editor <?php echo is_plugin_active('classic-editor/classic-editor.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
		<li>WP-REST-API V2 Menus <?php echo is_plugin_active('wp-rest-api-v2-menus/wp-rest-api-v2-menus.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
		<li>Stori Content Setup <?php echo is_plugin_active('stori-content-setup/index.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
		<li>Stori Theme <?php echo get_option('template') == 'stori-admin-theme' ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
	 <ul>

	 <h2>Release Notes</h2>
	 <p><strong>1.0.0</strong>
		<ul>
			<li>First Release</li>
		</ul>
	 <p><strong>1.1.0</strong>
		<ul>
			<li>Fixed Custom Post Types endpoints</li>
			<li>Disabled default WordPress endpoints</li>
			<li>Disabed X-WP-Nounce authentication and replace it using JWT token authentication</li>
		<ul>	
	 </p>
	 <p><strong>1.2.0</strong>
		<ul>
			<li>Added GraphQL support</li>
			<li>Fixed GraphQL editor JS error</li>
			<li>Fixed JWT token authentication error</li>
			<li>Use HEADLESS_MODE_CLIENT_URL to redirect client to other url instead admin if set</li>
			<li>Uses site_url() instead of home_url() to build the API URL</li>
			<li>Publish to netlify for headless production</li>	
			<li>Enable Cors for non-local urls</li>
		<ul>	
	 </p>
<?php
}

add_action('admin_menu', 'stori_admin_menu');


/**
 * Change editor for templates
 *
 * @return void
 */
function stori_templates_editor_change() {
	global $post;

	if (get_post_type() == 'stori_template') {
		$r = 'html';
	} else {
		$r = 'tinymce';
	}
	
	return $r;
}

add_filter('wp_default_editor', 'stori_templates_editor_change');

/**
 * Undocumented function
 *
 * @return void
 */
function stori_monaco_enqueue_scripts() {
	global $post;
	
	if (get_post_type() == 'stori_template') {
		wp_register_script('monaco-loader', 'https://unpkg.com/monaco-editor@0.15.6/min/vs/loader.js', array('wp-backbone'), false, true);
		wp_register_script('monaco', plugin_dir_url(__FILE__) . 'js/plugin-monaco.js', array('monaco-loader'), false, true);
		wp_enqueue_script(array('monaco-loader', 'monaco'));	
	}
}

add_action('admin_enqueue_scripts', 'stori_monaco_enqueue_scripts', 99, 0);

/**
 * Custom css for admin UI
 *
 * @return void
 */
function stori_enqueue_styles() {
	wp_register_style('stori-content-setup-style', plugin_dir_url(__FILE__) . 'css/global.css');
	wp_enqueue_style('stori-content-setup-style');
}

add_action('admin_enqueue_scripts', 'stori_enqueue_styles');

/**
 * Undocumented function
 *
 * @param [type] $api
 * @return void
 */
function stori_acf_google_map_api($api) {
	if (defined('GOOGLE_MAPS_V3_API_KEY')) {
		$api['key'] = GOOGLE_MAPS_V3_API_KEY;
	}
	
	return $api;
}

add_filter('acf/fields/google_map/api', 'stori_acf_google_map_api');

/**
 * Enable Application password authentication for API
 *
 * @param [type] $enable
 * @return void
 */
function stori_two_factor_user_api_login_enable($enable) {
    if (did_action('application_password_did_authenticate')) {
        return true;
    }

    return $enable;
}

add_filter('two_factor_user_api_login_enable', 'stori_two_factor_user_api_login_enable');

