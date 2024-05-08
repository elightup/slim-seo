<?php
namespace SlimSEO;

use WP_REST_Server;
use WP_REST_Request;

class RestApi {
	public function setup() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		register_rest_route( 'slim-seo', 'posts/(?P<id>\d+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_post_data' ],
			'permission_callback' => '__return_true',
		] );
	}

	public function get_post_data( WP_REST_Request $request ): array {
		return get_post_meta( $request->get_param( 'id' ), 'slim_seo', true ) ?: [];
	}
}