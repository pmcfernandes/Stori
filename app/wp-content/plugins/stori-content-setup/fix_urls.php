<?php
defined('ABSPATH') or die('No script kiddies please!');

function stori_fix_api_url($url) {
	$url = str_replace(home_url(), site_url(), $url);
	return $url;
}

add_filter('rest_url', 'stori_fix_api_url');

function stori_fix_preview_url($url) {
	$url = str_replace(home_url(), site_url(), $url);
	return $url;
}

add_filter('preview_post_link', 'stori_fix_preview_url');

function stori_fix_unpublished_post_url($url, $post_id) {
	if (get_post_status($post_id) != 'publish') {
	    $url = str_replace(home_url(), site_url(), $url);
	}

	return $url;
}

foreach(['post', 'page', 'attachment', 'post_type'] as $type) {
	add_filter($type . '_link', 'stori_fix_unpublished_post_url', 10, 2);
}
