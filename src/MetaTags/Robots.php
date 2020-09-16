<?php
namespace SlimSEO\MetaTags;

class Robots {
	use Context;

	private $url;

	public function __construct( CanonicalUrl $url ) {
		$this->url = $url;
	}

	public function setup() {
		// Priority 5 to be able to remove canonical link.
		add_action( 'wp_head', [ $this, 'output' ], 5 );
		add_action( 'template_redirect', [ $this, 'set_header_noindex' ] );
		add_filter( 'loginout', [ $this, 'set_link_nofollow' ] );
		add_filter( 'register', [ $this, 'set_link_nofollow' ] );
	}

	public function output() {
		$is_indexed = $this->is_indexed();
		$is_indexed = apply_filters( 'slim_seo_robots_index', $is_indexed );

		if ( $is_indexed ) {
			echo '<meta name="robots" content="max-snippet:-1, max-image-preview:large, max-video-preview:-1">';
			return;
		}

		// No index.
		wp_no_robots();
		remove_action( 'wp_head', [ $this->url, 'output' ] );
		remove_action( 'wp_head', 'rel_canonical' );
	}

	private function is_indexed() {
		// Do not index search or 404 page.
		if ( is_search() || is_404() ) {
			return false;
		}

		// Do not index private posts.
		if ( is_singular() && 'private' === get_queried_object()->post_status ) {
			return false;
		}

		// Do not index pages with no content.
		if ( ! is_front_page() && ! have_posts() ) {
			return false;
		}

		$noindex = $this->get_value();
		return ! $noindex;
	}

	private function get_singular_value() {
		$data = get_post_meta( get_queried_object_id(), 'slim_seo', true );
		return isset( $data['noindex'] ) ? $data['noindex'] : null;
	}

	private function get_term_value() {
		$data = get_term_meta( get_queried_object_id(), 'slim_seo', true );
		return isset( $data['noindex'] ) ? $data['noindex'] : null;
	}

	/**
	 * Set noindex for X-Robots-Tag header to make pages noindexed.
	 */
	public function set_header_noindex() {
		if ( ( is_feed() || is_robots() ) && headers_sent() === false ) {
			header( 'X-Robots-Tag: noindex, follow', true );
		}
	}

	public function set_link_nofollow( $link ) {
		return str_replace( '<a ', '<a rel="nofollow" ', $link );
	}
}
