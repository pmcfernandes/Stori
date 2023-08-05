<?php
defined('ABSPATH') or die('No script kiddies please!');


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
            'key' 				=> 'content_type_fields_group',
            'title' 				=> __('Features'),
            'fields' 				=> array (
                  array (
                        'key' 		=> 'description',
                        'label' 		=> 'Description',
                        'name'		=> 'description',
                        'type' 		=> 'textarea',
                        'rows'		=> 3,
                        'maxlength'		=> 512,
                        'required' 		=> 0
                  ),
                  array (
                        'key' 		=> 'supports',
                        'label' 		=> 'Supported features',
                        'name'		=> 'supports',
                        'type' 		=> 'checkbox',
                        'choices' 		=> array(
                              'title'	=> 'Title',
                              'excerpt'	=> 'Excerpt',
                              'editor'	=> 'Editor',
                              'author'	=> 'Author',
                              'thumbnail'	=> 'Thumbnail'
                        ),
                        'layout' 		=> 'vertical',
                        'return_format' 	=> 'value',
                        'required' 		=> 0
                  ),
                  array (
                        'key' 		=> 'taxonomies',
                        'label' 		=> 'Taxonomies',
                        'name'		=> 'taxonomies',
                        'type' 		=> 'checkbox',
                        'choices' 		=> array(
                              'category'	=> 'Categories',
                              'post_tag'	=> 'Tags'
                        ),
                        'layout' 		=> 'vertical',
                        'return_format' 	=> 'value',
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
            'instruction_placement' 	=> 'label',
            'hide_on_screen' 			=> '',
      ));
}

add_action('acf/init', 'stori_acf_content_type_fields');
 