<?php
namespace SlimSEO\MetaTags;

class Robots {
	use Context;

	private $url;

	public function __construct( CanonicalUrl $url ) {
		$this->url = $url;
	}

	public function setup() {
		if ( '0' === get_option( 'blog_public' ) ) {
			return;
		}

		$is_from_5_7 = version_compare( get_bloginfo( 'version' ), '5.6.9', '>' ); // WP uses filter from version 5.7.
		if ( $is_from_5_7 ) {
			add_filter( 'wp_robots', [ $this, 'modify_robots' ] );
		} else {
			add_action( 'wp_head', [ $this, 'output' ], 5 ); // Priority 5 to be able to remove canonical link.
		}

		add_action( 'template_redirect', [ $this, 'set_header_noindex' ] );
		add_filter( 'loginout', [ $this, 'set_link_nofollow' ] );
		add_filter( 'register', [ $this, 'set_link_nofollow' ] );
	}

	public function modify_robots( $robots ) {
		$is_indexed = $this->is_indexed();
		if ( $is_indexed ) {
			$robots['max-snippet']       = '-1';
			$robots['max-video-preview'] = '-1';
			return $robots;
		}

		// No index.
		$this->remove_canonical_link();
		return [
			'noindex' => true,
			'follow'  => true,
		];
	}

	public function output() {
		$is_indexed = $this->is_indexed();
		if ( $is_indexed ) {
			echo '<meta name="robots" content="max-snippet:-1, max-image-preview:large, max-video-preview:-1">';
			return;
		}

		// No index.
		wp_no_robots();
		$this->remove_canonical_link();
	}

	private function remove_canonical_link() {
		remove_action( 'wp_head', [ $this->url, 'output' ] );
		remove_action( 'wp_head', 'rel_canonical' );
	}

	private function is_indexed() {
		$value = $this->get_indexed();
		return apply_filters( 'slim_seo_robots_index', $value );
	}

	private function get_indexed() {
		// Do not index search or 404 page.
		if ( is_search() || is_404() ) {
			return false;
		}

		// Do not index private posts.
		if ( is_singular() && 'private' === get_queried_object()->post_status ) {
			return false;
		}

		// Do not index pages with no content.
		global $wp_query;
		if ( ! is_front_page() && ! $wp_query->post_count ) {
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
