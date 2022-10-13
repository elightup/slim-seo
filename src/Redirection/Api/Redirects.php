<?php
namespace SlimSEO\Redirection\Api;

use WP_REST_Server;
use WP_REST_Request;
use SlimSEO\Redirection\Database\Redirects as DbRedirects;

class Redirects extends Base {
	public function register_routes() {
		register_rest_route( 'slim-seo-redirection', 'redirects', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_redirects' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'is_exists', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'is_exists' ],
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

	public function get_redirects( WP_REST_Request $request ) : array {
		$db_redirects = new DbRedirects;
		$redirects    = $db_redirects->list();
		$redirects    = array_map( function( $index, $redirect ) {
			$redirect['id'] = $index;

			return $redirect;
		}, array_keys( $redirects ), $redirects );
		$redirects    = array_reverse( $redirects );

		return $redirects;
	}

	public function is_exists( WP_REST_Request $request ) : bool {
		$from         = $request->get_param( 'from' );
		$db_redirects = new DbRedirects;

		return $db_redirects->is_exists( $from );
	}

	public function update_redirect( WP_REST_Request $request ) : bool {
		$redirect     = $request->get_param( 'redirect' );
		$db_redirects = new DbRedirects;

		$db_redirects->update( $redirect );

		return true;
	}

	public function delete_redirects( WP_REST_Request $request ) : bool {
		$ids          = $request->get_param( 'ids' );
		$db_redirects = new DbRedirects;

		$db_redirects->delete( $ids );

		return true;
	}
}
