<?php
namespace SlimSEO\Redirection\Database;

use SlimSEO\Redirection\Helper;

class Redirects {
	protected $redirects;

	public function __construct() {
		$this->redirects = get_option( SLIM_SEO_REDIRECTS ) ?: [];
	}

	public function list() : array {
		return $this->redirects;
	}

	public function exists( string $url ) : bool {
		$url = Helper::normalize_url( $url );

		return count( array_filter( $this->redirects, function( $redirect ) use ( $url ) {
			return $redirect['from'] === $url;
		} ) ) > 0;
	}

	public function update( array $redirect ) {
		$redirect['from'] = Helper::normalize_url( $redirect['from'], false );
		$redirect['to']   = Helper::normalize_url( $redirect['to'] );
		$redirect['note'] = sanitize_text_field( $redirect['note'] );
		$redirect_id      = empty( $redirect['id'] ) ? uniqid( wp_rand() ) : $redirect['id'];

		unset( $redirect['id'] );
		$this->redirects[ $redirect_id ] = $redirect;

		update_option( SLIM_SEO_REDIRECTS, $this->redirects );
	}

	public function delete( array $ids ) {
		$this->redirects = array_filter( $this->redirects, function( $id ) use ( $ids ) {
			return ! in_array( $id, $ids, true );
		}, ARRAY_FILTER_USE_KEY );

		update_option( SLIM_SEO_REDIRECTS, $this->redirects );
	}
}
