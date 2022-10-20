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
		$redirect    = wp_unslash( $redirect );
		$redirect_id = $redirect['id'] ?? -1;

		unset( $redirect['id'] );

		$redirect['from'] = Helper::normalize_url( $redirect['from'] );
		$redirect['to']   = Helper::normalize_url( $redirect['to'] );
		$redirect['note'] = sanitize_text_field( $redirect['note'] );

		if ( -1 === $redirect_id ) {
			$this->redirects[] = $redirect;
		} else {
			$this->redirects[ $redirect_id ] = $redirect;
		}

		update_option( SLIM_SEO_REDIRECTS, $this->redirects );
	}

	public function delete( array $ids ) {
		$this->redirects = array_filter( $this->redirects, function( $id ) use ( $ids ) {
			return ! in_array( $id, $ids, true );
		}, ARRAY_FILTER_USE_KEY );

		update_option( SLIM_SEO_REDIRECTS, $this->redirects );
	}
}
