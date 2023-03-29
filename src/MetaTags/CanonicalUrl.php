<?php
namespace SlimSEO\MetaTags;

class CanonicalUrl {
	use Context;

	public function setup() {
		remove_action( 'wp_head', 'rel_canonical' );
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	public function output() {
		$url = $this->get_url();

		if ( $url ) {
			echo '<link rel="canonical" href="', esc_url( $url ), '" />', "\n";
		}
	}

	public function get_url() {
		$url = $this->get_value();
		$url = $this->add_pagination( $url );
		$url = apply_filters( 'slim_seo_canonical_url', $url, $this );
		$url = $this->normalize( $url );

		return $url;
	}

	private function get_home_value() {
		return home_url( '/' );
	}

	private function get_singular_value( $post_id = null ) {
		$post_id = $post_id ?: $this->get_queried_object_id();
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$data = get_post_meta( $post_id, 'slim_seo', true );
		if ( ! empty( $data['canonical'] ) ) {
			return $data['canonical'];
		}

		return wp_get_canonical_url( $this->get_queried_object() );
	}

	private function normalize( $url ) {
		return Helper::normalize( $url );
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
		if ( get_option( 'permalink_structure' ) ) {
			$url = trailingslashit( $url ) . 'page/' . user_trailingslashit( $paged, 'paged' );
		} else {
			$url = add_query_arg( 'paged', $paged, $url );
		}
		return $url;
	}
}
