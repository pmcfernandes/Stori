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
    'id'		    => $data->data['id'],
    'slug'		  => $data->data['slug'],
    'date'		  => $data->data['date'],
    'title'    	=> $data->data['title']['rendered'],
    'excerpt'   => $data->data['excerpt']['rendered'],
    'content'   => $data->data['content']['rendered'],
    'link'     	=> $data->data['link'],
    'author'    => $data->data['author'],
    'media'     => $data->data['featured_media'],
    'categories'=> $data->data['categories'],
    'tags'      => $data->data['tags'],
    'meta'      => array(
      'template'  => $data->data['template'],
      'format'  => $data->data['format']
    )    
	];
}

add_filter('rest_prepare_post', 'stori_get_all_posts', 10, 3);

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
    'id'		    => $data->data['id'],
    'slug'		  => $data->data['slug'],
    'date'		  => $data->data['date'],
    'parent'		=> $data->data['parent'],
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
    'id'		    => $data->data['id'],
    'name'		  => $data->data['name'],
    'slug'		  => $data->data['slug'],
    'avatar'		=> $data->data['avatar_urls']["48"],    
	];
}

add_filter('rest_prepare_user', 'stori_get_all_users', 10, 3); 

/**
 * Undocumented function
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_content_types($data, $post, $context) {
  return [
    'id'		    => $data->data['id'],
    'slug'		  => $data->data['slug'],
    'title'    	=> $data->data['title']['rendered']
	];
}

add_filter('rest_prepare_stori_content_type', 'stori_get_all_content_types', 10, 3);

/**
 * Undocumented function
 *
 * @param [type] $data
 * @param [type] $post
 * @param [type] $context
 * @return void
 */
function stori_get_all_templates($data, $post, $context) {
  return [
    'id'		    => $data->data['id'],
    'slug'		  => $data->data['slug'],
    'title'    	=> $data->data['title']['rendered'],
    'content'   => $data->data['content']['rendered'],
    'link'     	=> $data->data['link']
	];
}

add_filter('rest_prepare_stori_template', 'stori_get_all_templates', 10, 3);

/*

function get_latest_post ($params){
    $post = get_posts(array(
        'category' => $category,
        'posts_per_page'  => 1,
        'offset'      => 0
    ));

    if (empty($post)){
        return null;
    }

    return $post[0]->post_title;
}

function register_get_latest_post_endpoint() {
    register_rest_route('mynamespace/v1', 'latest-post', array(
        'methods'  => 'GET',
        'callback' => 'get_latest_post'
    ));
}

add_action('rest_api_init', 'register_get_latest_post_endpoint');     
  

class My_Rest_Server extends WP_REST_Controller {
 
    //The namespace and version for the REST SERVER
    var $my_namespace = 'my_rest_server/v';
    var $my_version   = '1';
   
    public function register_routes() {
      $namespace = $this->my_namespace . $this->my_version;
      $base      = 'category';
      register_rest_route( $namespace, '/' . $base, array(
        array(
            'methods'         => WP_REST_Server::READABLE,
            'callback'        => array( $this, 'get_latest_post' ),
            'permission_callback'   => array( $this, 'get_latest_post_permission' )
          ),
        array(
            'methods'         => WP_REST_Server::CREATABLE,
            'callback'        => array( $this, 'add_post_to_category' ),
            'permission_callback'   => array( $this, 'add_post_to_category_permission' )
          )
      )  );
    }
   
    // Register our REST Server
    public function hook_rest_server(){
      add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }
   
    public function get_latest_post_permission(){
      if ( ! current_user_can( 'edit_posts' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this data.', 'my-text-domain' ), array( 'status' => 401 ) );
        }
   
        // This approach blocks the endpoint operation. You could alternatively do this by an un-blocking approach, by returning false here and changing the permissions check.
        return true;
    }
   
    public function get_latest_post( WP_REST_Request $request ){
      //Let Us use the helper methods to get the parameters
      $category = $request->get_param( 'category' );
      $post = get_posts( array(
            'category'      => $category,
              'posts_per_page'  => 1,
              'offset'      => 0
      ) );
   
        if( empty( $post ) ){
          return null;
        }
   
        return $post[0]->post_title;
    }
   
    public function add_post_to_category_permission(){
      if ( ! current_user_can( 'edit_posts' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to create data.', 'my-text-domain' ), array( 'status' => 401 ) );
        }
        return true;
    }
   
    public function add_post_to_category( WP_REST_Request $request ){
      //Let Us use the helper methods to get the parameters
      $args = array(
        'post_title' => $request->get_param( 'title' ),
        'post_category' => array( $request->get_param( 'category' ) )
      );
   
      if ( false !== ( $id = wp_insert_post( $args ) ) ){
        return get_post( $id );
      }
   
      return false;
      
      
    }
  }
   
  $my_rest_server = new My_Rest_Server();
  $my_rest_server->hook_rest_server();

  */