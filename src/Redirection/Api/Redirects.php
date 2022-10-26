<?php
namespace SlimSEO\Redirection\Api;

use WP_REST_Server;
use WP_REST_Request;
use SlimSEO\Redirection\Database\Redirects as DbRedirects;

class Redirects extends Base {
	protected $db_redirects;

	public function __construct( DbRedirects $db_redirects ) {
		parent::__construct();
		$this->db_redirects = $db_redirects;
	}

	public function register_routes() {
		register_rest_route( 'slim-seo-redirection', 'redirects', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_redirects' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'exists', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'exists' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'update_redirect', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'update_redirect' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'delete_redirects', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'delete_redirects' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function get_redirects() : array {
		$redirects = $this->db_redirects->list();
		$redirects = array_map( function( $index, $redirect ) {
			$redirect['id'] = $index;

			return $redirect;
		}, array_keys( $redirects ), $redirects );
		$redirects = array_reverse( $redirects );
		return $redirects;
	}

	public function exists( WP_REST_Request $request ) : bool {
		$from = $request->get_param( 'from' );

		return $this->db_redirects->exists( $from );
	}

	public function update_redirect( WP_REST_Request $request ) : bool {
		$redirect = $request->get_param( 'redirect' );

		$this->db_redirects->update( $redirect );

		return true;
	}

	public function delete_redirects( WP_REST_Request $request ) : bool {
		$ids = $request->get_param( 'ids' );

		$this->db_redirects->delete( $ids );

		return true;
	}
}
