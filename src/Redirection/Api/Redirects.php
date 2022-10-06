<?php
namespace SlimSEO\Redirection\Api;

use SlimSEO\Redirection\Redirects as DbRedirects;
use WP_REST_Server;
use WP_REST_Request;

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
		$redirects = DbRedirects::list();
		$redirects = array_map( function( $index, $redirect ) {
			$redirect['id'] = $index;
			
			return $redirect;
		}, array_keys( $redirects ), $redirects );
		$redirects = array_reverse( $redirects );

		return $redirects;
	}

	public function is_exists( WP_REST_Request $request ) : bool {
		$from = $request->get_param( 'from' );
		
		return DbRedirects::is_exists( $from );
	}

	public function update_redirect( WP_REST_Request $request ) : bool {
		$redirect = $request->get_param( 'redirect' );
		
		DbRedirects::update( $redirect );
	
		return true;
	}

	public function delete_redirects( WP_REST_Request $request ) : bool {
		$ids = $request->get_param( 'ids' );
		
		DbRedirects::delete( $ids );
	
		return true;
	}
}