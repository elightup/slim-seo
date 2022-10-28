<?php
namespace SlimSEO\Redirection\Api;

use WP_REST_Server;
use WP_REST_Request;
use SlimSEO\Redirection\Settings;
use SlimSEO\Redirection\Database\Log404 as DbLog;

class Log404 extends Base {
	protected $db_log;

	public function __construct( DbLog $db_log ) {
		parent::__construct();
		$this->db_log = $db_log;
	}

	public function register_routes() {
		if ( ! Settings::get( 'enable_404_logs' ) ) {
			return;
		}

		register_rest_route( 'slim-seo-redirection', 'total_logs', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_total' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'logs', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_logs' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'delete_log', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'delete_log' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'delete_logs', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'delete_logs' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function get_total() : int {
		return $this->db_log->get_total();
	}

	public function get_logs( WP_REST_Request $request ) : array {
		$order_by = sanitize_text_field( $request->get_param( 'orderBy' ) );
		$sort     = sanitize_text_field( $request->get_param( 'sort' ) );
		$limit    = (int) $request->get_param( 'limit' );
		$offset   = (int) $request->get_param( 'offset' );

		return $this->db_log->list( $order_by, $sort, $limit, $offset );
	}

	public function delete_log( WP_REST_Request $request ) {
		$id = (int) $request->get_param( 'id' );

		$this->db_log->delete( $id );

		return true;
	}

	public function delete_logs() {
		$this->db_log->delete_all();

		return true;
	}
}
