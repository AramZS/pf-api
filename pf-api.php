<?php
/*
Plugin Name: PressForward API
Plugin URI: http://pressforward.org/
Description: The PressForward Plugin is a tool by the Roy Rosenzweig Center for History and New Media for aggregating and curating web-based content within the WordPress dashboard.
Version: 3.6.1
GitHub Plugin URI: https://github.com/PressForward/pressforward
Author: Aram Zucker-Scharff
Author URI: http://pressforward.org/about/team/
License: GPL2
*/
class PF_API {

	function __construct(){
		add_action( 'wp_json_server_before_serve', array( $this, 'myplugin_api_init' ) );
	}

	function myplugin_api_init() {
		global $myplugin_api_mytype;

		$myplugin_api_mytype = new MyPlugin_API_MyType();
		add_filter( 'json_endpoints', array( $myplugin_api_mytype, 'register_routes' ) );
	}

}

class MyPlugin_API_MyType {

	public function register_routes( $routes ) {
		$routes['/pressforward/'] = array(
			array( array( $this, 'new_item'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
		);
		$routes['/pressforward/meta'] = array(
			array( array( $this, 'pf_metas'), WP_JSON_Server::READABLE )
		);
		$routes['/pressforward/nomination/(?P<id>\d+)'] = array(
			array( array( $this, 'get_post'), WP_JSON_Server::READABLE ),
			array( array( $this, 'edit_post'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
			array( array( $this, 'delete_post'), WP_JSON_Server::DELETABLE ),
		);

		$routes['/posts/(?P<id>\d+)/pf'] = array(
			array( array( $this, 'data'), WP_JSON_Server::READABLE )
		);
		$pf_routes = array(
				'item_id',
				'source_title',
				'item_date',
				'item_author',
				'item_link',
				'item_feat_img',
				'item_wp_date',
				'item_tags',
				'source_repeat',
				'readable_status',
				'pf_feed_item_word_count'
			);
		foreach ( $pf_routes as $pf_route ){
			$routes['/posts/(?P<id>\d+)/pf/'.$pf_route] = array(
				array(
					array( $this, $pf_route),
					WP_JSON_Server::READABLE
				)
			);
		}

		// Add more custom routes here

		return $routes;
	}

	public function data(){
		return array( 'result' => 'bob' );
	}

	public function item_id( $id ){
		return array( 'result' => get_post_meta( $id, 'origin_item_ID' ) );
	}

	public function meta(){
		return array( 'result' => 'meta' );
	}

	// ...
}

new PF_API;