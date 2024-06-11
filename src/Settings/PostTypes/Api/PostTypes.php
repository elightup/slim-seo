<?php
namespace SlimSEO\Settings\PostTypes\Api;
use WP_REST_Server;
use WP_REST_Request;

use SlimSEO\Helpers\Data;

class PostTypes {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		register_rest_route( 'slim-seo-post-types', 'post_types', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_post_types' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function has_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	public function get_post_types( WP_REST_Request $request ): array {
		return array_diff_key( Data::get_post_types(), array_flip( [ 'post', 'page' ] ) );
	}
}
