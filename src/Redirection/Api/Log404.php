<?php
namespace SlimSEO\Redirection\Api;

use WP_REST_Server;
use WP_REST_Request;
use SlimSEO\Redirection\Helper;
use SlimSEO\Redirection\Redirection404;

class Log404 extends Base {
	public function register_routes() {
		if ( ! Helper::get_setting( 'enable_404_logs' ) ) {
			return;
		}

		register_rest_route( 'slim-seo-redirection', 'total_404_logs', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_total_logs' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'get_404_logs', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'get_logs' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function get_total_logs( WP_REST_Request $request ) : int {
		return Redirection404::get_total_logs();
	}

	public function get_logs( WP_REST_Request $request ) : array {
		$order  = $request->get_param( 'order' );
		$limit  = $request->get_param( 'limit' );
		$offset = $request->get_param( 'offset' );

		return Redirection404::get_logs_list( $order['orderBy'], $order['sort'], intval( $limit ), intval( $offset ) );
	}
}
