<?php
namespace SlimSEO\MetaTags;

class Robots {
	public function __construct() {
		add_action( 'wp_head', [ $this, 'output' ] );
		add_action( 'template_redirect', [ $this, 'set_header_noindex' ] );
		add_filter( 'loginout', [ $this, 'set_link_nofollow' ] );
		add_filter( 'register', [ $this, 'set_link_nofollow' ] );
	}

	public function output() {
		$is_indexed = $this->is_indexed();
		$is_indexed = apply_filters( 'slim_seo_robots_index', $is_indexed );
		if ( ! $is_indexed ) {
			wp_no_robots();
		}
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

		return true;
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
