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

		$args = [
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => [ $this, 'has_permission' ],
			'show_in_index'       => false,
		];
		register_rest_route( 'slim-seo-redirection', 'records/total', array_merge( $args, [
			'callback' => [ $this, 'total' ],
		] ) );
		register_rest_route( 'slim-seo-redirection', 'records/list', array_merge( $args, [
			'callback' => [ $this, 'list' ],
		] ) );
		register_rest_route( 'slim-seo-redirection', 'records/delete', array_merge( $args, [
			'callback' => [ $this, 'delete' ],
		] ) );
		register_rest_route( 'slim-seo-redirection', 'records/delete-all', array_merge( $args, [
			'callback' => [ $this, 'delete_all' ],
		] ) );
	}

	public function total(): int {
		return $this->db_log->get_total();
	}

	public function list( WP_REST_Request $request ): array {
		$order_by         = sanitize_text_field( $request->get_param( 'orderBy' ) );
		$sort             = sanitize_text_field( $request->get_param( 'sort' ) );
		$limit            = (int) $request->get_param( 'limit' );
		$offset           = (int) $request->get_param( 'offset' );
		$allowed_order_by = [ 'updated_at', 'hit', 'created_at' ];
		$allowed_sort     = [ 'asc', 'desc' ];

		if ( ! in_array( $order_by, $allowed_order_by, true ) ) {
			$order_by = 'updated_at';
		}

		if ( ! in_array( $sort, $allowed_sort, true ) ) {
			$sort = 'desc';
		}

		return $this->db_log->list( $order_by, $sort, $limit, $offset );
	}

	public function delete( WP_REST_Request $request ) {
		$id = (int) $request->get_param( 'id' );

		$this->db_log->delete( $id );

		return true;
	}

	public function delete_all() {
		$this->db_log->delete_all();

		return true;
	}
}
