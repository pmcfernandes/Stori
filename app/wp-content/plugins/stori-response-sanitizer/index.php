<?php 

/** 
* Plugin Name:       Stori Response Sanitizer
* Plugin URI:        
* Description:       Add and remove features from WP_API
* Version:           1.0.0
* Author:            Patreo
* Author URI:        http://patreo.com
* License:           MIT
* License URI:       
* Text Domain:       stori-response-sanitizer
*/

defined('ABSPATH') or die('No script kiddies please!');
 
 
/**
 * Change name of default rest url to 'api' prefix
 *
 * @return {string}
 */
function stori_rest_url_prefix() {
    return 'api';
}

add_filter('rest_url_prefix', 'stori_rest_url_prefix');

/**
 * Posts result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_posts($data, $post, $context) {
	return [
        'id'		=> $data->data['id'],
        'slug'		=> $data->data['slug'],
        'date'		=> $data->data['date'],
        'title'    	=> $data->data['title']['rendered'],
        'excerpt'   => $data->data['excerpt']['rendered'],
        'content'   => $data->data['content']['rendered'],
        'link'     	=> $data->data['link'],
        'author'    => $data->data['author'],
        'media'     => $data->data['featured_media'] ?? 0,
        'categories'=> $data->data['categories'] ?? array(),
        'tags'      => $data->data['tags'] ?? array(),
        'meta'      => array(
            'template'  => $data->data['template'],
            'format'  => $data->data['format']
        )    
	];
}

add_filter('rest_prepare_post', 'stori_get_all_posts', 10, 3);

/**
 * Comments result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_comments($data, $post, $context) {
	return [
        'id'		=> $data->data['id'],
        'parent'	=> $data->data['parent'],
        'author'    => $data->data['author'],
        'date'		=> $data->data['date'],
        'content'   => $data->data['content']['rendered'],
        'link'     	=> $data->data['link'],
	];
}

add_filter('rest_prepare_comment', 'stori_get_all_comments', 10, 3);

/**
 * Page results sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_pages($data, $post, $context) {
	return [
        'id'		=> $data->data['id'],
        'slug'		=> $data->data['slug'],
        'date'		=> $data->data['date'],
        'parent'	=> $data->data['parent'],
        'title'    	=> $data->data['title']['rendered'],
        'excerpt'   => $data->data['excerpt']['rendered'],
        'content'   => $data->data['content']['rendered'],
        'link'     	=> $data->data['link'],
        'author'    => $data->data['author'],
        'media'     => $data->data['featured_media'],
        'meta'      => array(
        'template'  => $data->data['template'],
        )    
	];
}

add_filter('rest_prepare_page', 'stori_get_all_pages', 10, 3); 

/**
 * User result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_users($data, $post, $context) {
	return [
        'id'		  => $data->data['id'],
        'name'		  => $data->data['name'],
        'slug'		  => $data->data['slug'],
        'avatar'	  => $data->data['avatar_urls']["48"],    
	];
}

add_filter('rest_prepare_user', 'stori_get_all_users', 10, 3); 

/**
 * Categories result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_categories($data, $post, $context) {
  return [
    'id'		    => $data->data['id'],
    'count'		    => $data->data['count'],
    'parent'		=> $data->data['parent'],
    'slug'		    => $data->data['slug'],
    'name'		    => $data->data['name'],
    'description'	=> $data->data['description'],
    'link'		    => $data->data['link'],
	];
}

add_filter('rest_prepare_category', 'stori_get_all_categories', 10, 3);

/**
 * Statuses result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_statuses($data, $post, $context) {
  return [
    'name'	=> $data->data['name'],
    'slug'	=> $data->data['slug'],
 ];
}

add_filter('rest_prepare_status', 'stori_get_all_statuses', 10, 3);

/**
 * Taxonomy result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_taxonomies($data, $post, $context) {
    return [
        'name'	    => $data->data['name'],
        'slug'	    => $data->data['slug'],
        'description'	=> $data->data['description'],
        'rest'	    => $data->data['rest_base'],
    ];
}

add_filter('rest_prepare_taxonomy', 'stori_get_all_taxonomies', 10, 3);

/**
 * Post Tags result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_tags($data, $post, $context) {
    return [
        'name'	      => $data->data['name'],
        'slug'	      => $data->data['slug'],
        'description' => $data->data['description'],
        'count'	      => $data->data['count'],
    ];
}

add_filter('rest_prepare_post_tag', 'stori_get_all_tags', 10, 3);

/**
 * Post Types result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_post_types($data, $post, $context) {
  return [
    'name'	        => $data->data['name'],
    'slug'	        => $data->data['slug'],
    'description'	=> $data->data['description'],
    'rest'	        => $data->data['rest_base'],
    'taxonomies'	=> $data->data['taxonomies']    
  ];
}

add_filter('rest_prepare_post_type', 'stori_get_all_post_types', 10, 3);

/**
 * Media result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_media($data, $post, $context) {
   return [
      'id'	        => $data->data['id'],
      'date'	    => $data->data['date'],
      'slug'	    => $data->data['slug'],
      'title'	    => $data->data['title']['rendered'],
      'caption'	    => $data->data['caption']['rendered'],
      'alt_text'	=> $data->data['alt_text'],
      'type'	    => $data->data['media_type'],
      'mime_type'	=> $data->data['mime_type'],
      'author'	    => $data->data['author'],
      'related'	    => $data->data['post'],
      'link'	    => $data->data['source_url']    
    ];
}

add_filter('rest_prepare_attachment', 'stori_get_all_media', 10, 3);

/**
 * Content Types result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_content_types($data, $post, $context) {
  return [
    'id'		=> $data->data['id'],
    'slug'		=> $data->data['slug'],
    'title'    	=> $data->data['title']['rendered']
  ];
}

add_filter('rest_prepare_stori_content_type', 'stori_get_all_content_types', 10, 3);

/**
 * Templates result sanitizer
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_templates($data, $post, $context) {
  return [
    'id'		=> $data->data['id'],
    'slug'		=> $data->data['slug'],
    'title'    	=> $data->data['title']['rendered'],
    'content'   => $data->data['content']['rendered'],
    'link'     	=> $data->data['link']
	];
}

add_filter('rest_prepare_stori_template', 'stori_get_all_templates', 10, 3);

/**
 * Customize content types to new sanitize model and add custom fields from acf
 *
 * @return void
 */
function stori_rest_prepare_custom_types() {
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
            $post_name = get_post_field('post_name');
            
            add_filter('rest_prepare_' . $post_name, 'stori_get_all_content_types_data', 10, 3);            

            register_rest_field($post_name, 'post-meta-fields',
                array(
                    'get_callback'  => 'stori_get_custom_field',
                    'schema'        => 'fields'
                )
            );
		}
	}

	// Restore original Post Data
	wp_reset_postdata();
}

/**
 * Get custom fields from acf
 *
 * @param [type] $object
 * @param [type] $field_name
 * @param [type] $request
 * @return void
 */
function stori_get_custom_field($object, $field_name, $request) {
    return get_fields($object['id']);
}

/**
 * Sanitize content types data
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_content_types_data($data, $post, $context) {    
    $d = array_merge([
        'id'		=> $data->data['id'],
        'slug'		=> $data->data['slug'],
        'date'		=> $data->data['date'],
        'title'    	=> $data->data['title']['rendered'],
        'excerpt'   => $data->data['excerpt']['rendered'],
        'content'   => $data->data['content']['rendered'],
        'link'     	=> $data->data['link'],
        'author'    => $data->data['author'],
        'media'     => $data->data['featured_media'] ?? 0,
        'categories'=> $data->data['categories'] ?? array(),
        'tags'      => $data->data['tags'] ?? array()
    ], $data->data['post-meta-fields']);

    if (empty($d['title'])) {
        unset($d['title']);
    }

    if (empty($d['excerpt'])) {
        unset($d['excerpt']);
    }

    if (empty($d['content'])) {
        unset($d['content']);
    }

    if ($d['media'] == 0) {
        unset($d['media']);
    }

    if (count($d['categories']) == 0) {
        unset($d['categories']);
    }

    if (count($d['tags']) == 0) {
        unset($d['tags']);
    }

    return $d;
}

add_action('rest_api_init', 'stori_rest_prepare_custom_types');


