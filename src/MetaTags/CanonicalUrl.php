<?php
namespace SlimSEO\MetaTags;

class CanonicalUrl {
	public function setup() {
		add_action( 'wp_head', [ $this, 'output' ], 5 );
	}

	public function output() {
		$url = $this->get_url();
		if ( $url ) {
			echo '<link rel="canonical" href="', esc_url( $url ), '" />', "\n";
		}
	}

	public function get_url() {
		// WordPress already handles canonical URL for singular pages.
		if ( is_singular() ) {
			return '';
		}

		$url = '';
		if ( is_home() ) {
			$url = $this->get_home_url();
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$url = $this->get_term_url();
		} elseif ( is_post_type_archive() ) {
			$url = $this->get_post_type_archive_url();
		} elseif ( is_author() ) {
			$url = $this->get_author_url();
		}

		// As this is not a singular page, we should handle pagination.
		$url = $this->add_pagination( $url );

		$url = apply_filters( 'slim_seo_canonical_url', $url, $this );

		return $url;
	}

	public function get_home_url() {
		return is_front_page() ? home_url( '/' ) : get_permalink( get_queried_object() );
	}

	public function get_term_url() {
		return get_term_link( get_queried_object() );
	}

	public function get_post_type_archive_url() {
		return get_post_type_archive_link( get_post_type() );
	}

	public function get_author_url() {
		return get_author_posts_url( get_queried_object_id() );
	}

	private function add_pagination( $url ) {
		$paged = get_query_var( 'paged' );
		if ( $paged < 2 ) {
			return $url;
		}
		if ( '' == get_option( 'permalink_structure' ) ) {
			$url = add_query_arg( 'paged', $paged, $url );
		} else {
			$url = trailingslashit( $url ) . 'page/' . user_trailingslashit( $paged, 'paged' );
		}
		return $url;
	}
}