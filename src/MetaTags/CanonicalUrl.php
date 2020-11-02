<?php
namespace SlimSEO\MetaTags;

class CanonicalUrl {
	use Context;

	public function setup() {
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	public function output() {
		// WordPress already handles canonical URL for singular pages.
		if ( is_singular() ) {
			return;
		}
		$url = $this->get_url();
		if ( $url ) {
			echo '<link rel="canonical" href="', esc_url( $url ), '" />', "\n";
		}
	}

	public function get_url() {
		$url = $this->get_value();
		$url = $this->add_pagination( $url );
		$url = apply_filters( 'slim_seo_canonical_url', $url, $this );

		return $url;
	}

	private function get_home_value() {
		return home_url( '/' );
	}

	private function get_singular_value() {
		return wp_get_canonical_url( get_queried_object() );
	}

	private function get_term_value() {
		return get_term_link( get_queried_object() );
	}

	private function get_post_type_archive_value() {
		return get_post_type_archive_link( get_queried_object()->name );
	}

	private function get_author_value() {
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