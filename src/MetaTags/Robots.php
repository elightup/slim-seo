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

		add_filter( 'wp_robots', [ $this, 'modify_robots' ] );

		add_action( 'template_redirect', [ $this, 'set_header_noindex' ] );
		add_filter( 'loginout', [ $this, 'set_link_nofollow' ] );
		add_filter( 'register', [ $this, 'set_link_nofollow' ] );
	}

	public function modify_robots( $robots ) {
		if ( $this->indexed() ) {
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

	private function remove_canonical_link() {
		remove_action( 'wp_head', [ $this->url, 'output' ] );
		remove_action( 'wp_head', 'rel_canonical' );
	}

	private function indexed() {
		$value = $this->get_indexed();
		return apply_filters( 'slim_seo_robots_index', $value, get_queried_object_id() );
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

	/**
	 * Make public to allow access from other class.
	 * @see AdminColumns/Post.php.
	 */
	public function get_singular_value( $post_id = 0 ): bool {
		$post_id = $post_id ?: $this->get_queried_object_id();
		$data    = get_post_meta( $post_id, 'slim_seo', true );
		return isset( $data['noindex'] ) ? (bool) $data['noindex'] : false;
	}

	/**
	 * Make public to allow access from other class.
	 * @see AdminColumns/Term.php.
	 */
	public function get_term_value( $term_id = 0 ) : bool {
		$term_id = $term_id ?: get_queried_object_id();
		$data    = get_term_meta( $term_id, 'slim_seo', true );
		return isset( $data['noindex'] ) ? (bool) $data['noindex'] : false;
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
