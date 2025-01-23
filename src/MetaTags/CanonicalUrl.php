<?php
namespace SlimSEO\MetaTags;

defined( 'ABSPATH' ) || die;

class CanonicalUrl {
	use Context;

	public function setup() {
		remove_action( 'wp_head', 'rel_canonical' );
		add_action( 'slim_seo_head', [ $this, 'output' ] );
	}

	public function output(): void {
		$url = $this->get_url();
		if ( $url && filter_var( $url, FILTER_VALIDATE_URL ) ) {
			echo '<link rel="canonical" href="', esc_url( $url ), '">', "\n";
		}
	}

	public function get_url(): string {
		$url = $this->get_value();
		$url = $this->add_pagination( $url );
		$url = (string) apply_filters( 'slim_seo_canonical_url', $url, $this->get_queried_object_id() );
		$url = Helper::render( $url );

		return $url;
	}

	private function get_home_value(): string {
		return home_url( '/' );
	}

	private function get_singular_value(): string {
		$data = get_post_meta( $this->get_queried_object_id(), 'slim_seo', true );
		if ( ! empty( $data['canonical'] ) ) {
			return $data['canonical'];
		}

		$url = (string) get_permalink( $this->get_queried_object() );

		$page = get_query_var( 'page', 0 );
		if ( $page < 2 ) {
			return $url;
		}
		if ( get_option( 'permalink_structure' ) ) {
			$url = trailingslashit( $url ) . user_trailingslashit( $page, 'single_paged' );
		} else {
			$url = add_query_arg( 'page', $page, $url );
		}

		return $url;
	}

	private function get_term_value(): string {
		$data = get_term_meta( $this->get_queried_object_id(), 'slim_seo', true );
		if ( ! empty( $data['canonical'] ) ) {
			return $data['canonical'];
		}
		$url = get_term_link( $this->get_queried_object() );
		return is_string( $url ) ? $url : '';
	}

	private function get_post_type_archive_value(): string {
		return (string) get_post_type_archive_link( $this->get_queried_object()->name );
	}

	private function get_author_value(): string {
		return get_author_posts_url( $this->get_queried_object_id() );
	}

	private function add_pagination( string $url ): string {
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
