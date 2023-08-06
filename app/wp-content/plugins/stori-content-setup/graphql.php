<?php
defined('ABSPATH') or die('No script kiddies please!');

require_once (dirname(__FILE__) . '/inc/plural.php');

function stori_graphql_request_results($response) {
	if (is_array($response) && isset($response['extensions'])) {
		unset($response['extensions']);
	}
	
      if (is_object($response) && isset($response->extensions)) {
		unset($response->extensions);
	}

	return $response;
}


add_filter('graphql_request_results', 'stori_graphql_request_results', 99, 1);

function stori_register_post_type_args($args, $post_type) {
      $blocked = array(
            'post',
            'page',
            'attachment',
            'revision',
            'nav_menu_item',
            'custom_css',
            'customize_changeset',
            'oembed_cache',
            'user_request',
            'wp_block',
            'wp_template',
            'wp_template_part',
            'wp_global_styles',
            'wp_navigation',
            'acf-taxonomy',
            'acf-post-type',
            'acf-field-group',
            'acf-field'
      );

      if (in_array($post_type, $blocked)) {
            return $args;
      }

      $args['show_in_graphql'] = true;
      $args['graphql_single_name'] = $post_type;
      $args['graphql_plural_name'] = Inflect::pluralize($post_type);
      return $args;
}

add_filter('register_post_type_args', 'stori_register_post_type_args', 10, 2);


function stori_graphql_register_types() {
      $field_types = array(
            'text' => 'String',
            'textarea' => 'String',
            'number' => 'Float',
            'range' => 'Float',
            'email' => 'String',
            'url' => 'String',
            'password' => 'String',
            'image' => 'MediaItem',
            'file' => 'MediaItem',
            'wysiwyg' => 'String',
            'oembed' => 'String',
            'gallery' => ['list_of' => 'MediaItem'],
            'select' => 'String',
            'checkbox' => ['list_of' => 'String'],
            'radio' => 'String',
            'button_group' =>  ['list_of' => 'String'],
            'true_false' => 'Boolean',
            'link' => 'AcfLink',
            'post_object' => 'PostObjectUnion',
            'page_link' => 'PostObjectUnion',
            'relationship' => 'PostObjectUnion',
            'taxonomy' => 'TermObjectUnion',
            'user' => 'User',
            'google_map' => 'ACF_GoogleMap',
            'date_picker' => 'String',
            'date_time_picker' => 'String',
            'time_picker' => 'String',
            'color_picker' => 'String',
            'group' => 'String',
            'repeater' => 'String',
            'flexible_content' => 'String'
      );

      $blocked = array(
            'post',
		'page',
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
		'wp_template',
		'wp_template_part',
		'wp_global_styles',     
		'wp_navigation',
		'acf-taxonomy',
		'acf-post-type',
		'acf-field-group',
		'acf-field',
		'stori_content_type',
		'stori_template',
      );

      $post_types = get_post_types();

      foreach ($post_types as $key => $value) {
            if (!in_array($key, $blocked)) {
                  $groups = acf_get_field_groups(array('post_type' => $key));

                  foreach ($groups as $group) {
                        $fields = acf_get_fields($group['key']);

                        foreach ($fields as $field) {
                              register_graphql_field($key, $field['name'], array(
                                    'type' => $field_types[$field['type']],
                                    'description' => $field['label'],
                                    'resolve' => function( \WPGraphQL\Model\Post $post, $args, $context, $info) use ($field) {
                                          $data = get_field($field['name'], $post->ID);              
                                          return $data;
                                    }
                              ));
                        }
                  }
            }
      }
}

add_action('graphql_register_types', 'stori_graphql_register_types');
