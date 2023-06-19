<?php 
defined('ABSPATH') or die('No script kiddies please!');

include_once(dirname(__FILE__) . '/inc/plural.php');

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
	 	<li>Advanced Custom Fields <?php echo is_plugin_active('advanced-custom-fields/acf.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
		<li>Application Passwords <?php echo is_plugin_active('application-passwords/application-passwords.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
		<li>Classic Editor <?php echo is_plugin_active('classic-editor/classic-editor.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
		<li>WP-REST-API V2 Menus <?php echo is_plugin_active('wp-rest-api-v2-menus/wp-rest-api-v2-menus.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
		<li>Stori Content Setup <?php echo is_plugin_active('stori-content-setup/index.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
		<li>Stori Response Sanitizer <?php echo is_plugin_active('stori-response-sanitizer/index.php') ? "<span style=\"color:green\">Installed</span>" : "<span style=\"color:red\">Not Installed</span>" ?></li>
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
<?php
}

add_action('admin_menu', 'stori_admin_menu');

function stori_register_custom_types($type = 'post', $name, $title, $description = '', $rewrite = null,  $supports = array('title'), $taxonomies = array('category'), $show_in_menu = true) {	
	$plural = Inflect::pluralize($title);

	$labels = array(
		'name'                  => _x( $plural, 'Post Type General Name', 'stori-content-setup' ),
		'singular_name'         => _x( $title, 'Post Type Singular Name', 'stori-content-setup' ),
		'menu_name'             => __( $plural, 'stori-content-setup' ),
		'name_admin_bar'        => __( $title, 'stori-content-setup' ),
		'archives'              => __( 'Item Archives', 'stori-content-setup' ),
		'attributes'            => __( 'Item Attributes', 'stori-content-setup' ),
		'parent_item_colon'     => __( 'Parent ' . $title . ':', 'stori-content-setup' ),
		'all_items'             => __( 'All ' . $plural, 'stori-content-setup' ),
		'add_new_item'          => __( 'Add New ' . $title, 'stori-content-setup' ),
		'add_new'               => __( 'Add New', 'stori-content-setup' ),
		'new_item'              => __( 'New ' . $title, 'stori-content-setup' ),
		'edit_item'             => __( 'Edit ' . $title, 'stori-content-setup' ),
		'update_item'           => __( 'Update ' . $title, 'stori-content-setup' ),
		'view_item'             => __( 'View ' . $title, 'stori-content-setup' ),
		'view_items'            => __( 'View ' . $plural, 'stori-content-setup' ),
		'search_items'          => __( 'Search ' . $title, 'stori-content-setup' ),
		'not_found'             => __( 'Not found', 'stori-content-setup' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'stori-content-setup' ),
		'featured_image'        => __( 'Featured Image', 'stori-content-setup' ),
		'set_featured_image'    => __( 'Set featured image', 'stori-content-setup' ),
		'remove_featured_image' => __( 'Remove featured image', 'stori-content-setup' ),
		'use_featured_image'    => __( 'Use as featured image', 'stori-content-setup' ),
		'insert_into_item'      => __( 'Insert into ' .  $title, 'stori-content-setup' ),
		'uploaded_to_this_item' => __( 'Uploaded to this ' . $title, 'stori-content-setup' ),
		'items_list'            => __( 'Items list', 'stori-content-setup' ),
		'items_list_navigation' => __( 'Items list navigation', 'stori-content-setup' ),
		'filter_items_list'     => __( 'Filter ' . $plural . ' list', 'stori-content-setup' ),
	);
	$args = array(
		'label'                 => __( $title, 'stori-content-setup' ),
		'description'           => __($description, 'stori-content-setup' ),
		'labels'                => $labels,
		'supports'              => $supports,
		'taxonomies'            => $taxonomies,
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => $show_in_menu,
		'menu_position'         => 2,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => $type,
		'rewrite' => array(
			'slug' => (empty($rewrite) ? $name : $rewrite)
		),
		'show_in_rest'			=> true,
		'rest_base'				=> $name		
     );
     
	register_post_type($name, $args);
}

/**
 * Load custom post types from database
 *
 * @return void
 */
function stori_load_custom_types() {
	// WP_Query arguments
	$args = array (
		'post_type'              => array('stori_content_type'),
		'post_status'            => array('publish'),
		'nopaging'               => true,
		'order'                  => 'ASC',
		'orderby'                => 'ID',
	);

	// The Query
	$contents = new WP_Query($args);

	// The Loop
	if ($contents->have_posts()) {
		while ($contents->have_posts()) {
			$contents->the_post();
			stori_register_custom_types('post'
				, get_post_field('post_name')
				, get_the_title()
				, get_field('description')
				, null
				, get_field('supports')
				, get_field('taxonomies'));
		}
	}

	// Restore original Post Data
	wp_reset_postdata();
}

/**
 * Register content setup type
 *
 * @return void
 */
function register_stori_content_setup_type() {
	stori_register_custom_types('post', 'stori_content_type', 'Content Type', 'Content Type', 'content-types', array('title'), array(), false);
}

/**
 * Register custom templates type
 *
 * @return void
 */
function register_stori_template_setup_type() {
	stori_register_custom_types('page', 'stori_template', 'Templates', 'Page Templates', 'templates', array('title', 'editor'), array(), false);
}

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
 * Init Stori.
 *
 * @return void
 */
function stori_init() {
     register_stori_content_setup_type();
	 register_stori_template_setup_type();
	 stori_load_custom_types();	 
}

add_action('init', 'stori_init');

/**
 * Add custom fields to Content Type Setup
 *
 * @return void
 */
function stori_acf_content_type_fields() {
		
	acf_add_local_field_group(array (
		'key' 					=> 'content_type_fields_group',
		'title' 				=> __('Features'),
		'fields' 				=> array (
			array (
				'key' 			=> 'description',
				'label' 		=> 'Description',
				'name'			=> 'description',
				'type' 			=> 'textarea',
				'rows'			=> 3,
				'maxlength'		=> 512,
				'required' 		=> 0
			),
			array (
				'key' 			=> 'supports',
				'label' 		=> 'Supported features',
				'name'			=> 'supports',
				'type' 			=> 'checkbox',
				'choices' 		=> array(
					'title'		=> 'Title',
					'excerpt'	=> 'Excerpt',
					'author'	=> 'Author',
					'thumbnail'	=> 'Thumbnail'
				),
				'layout' 		=> 'vertical',
				'return_format' => 'value',
				'required' 		=> 0
			),
			array (
				'key' 			=> 'taxonomies',
				'label' 		=> 'Taxonomies',
				'name'			=> 'taxonomies',
				'type' 			=> 'checkbox',
				'choices' 		=> array(
					'category'	=> 'Categories',
					'post_tag'	=> 'Tags'
				),
				'layout' 		=> 'vertical',
				'return_format' => 'value',
				'required' 		=> 0
			)
		),
		'location' => array (
			array (
				array (
					'param' 	=> 'post_type',
					'operator' 	=> '==',
					'value' 	=> 'stori_content_type',
				),
			),
		),
		'menu_order' 			=> 0,
		'position' 				=> 'normal',
		'style' 				=> 'default',
		'label_placement' 		=> 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' 		=> '',
	));
	
}

add_action('acf/init', 'stori_acf_content_type_fields');
	
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