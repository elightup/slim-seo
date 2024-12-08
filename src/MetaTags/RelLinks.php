<?php
namespace SlimSEO\MetaTags;

defined( 'ABSPATH' ) || die;

class RelLinks {
	use Context;

	public function setup() {
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	public function output() {
		// WordPress already handles rel URL for singular pages.
		if ( is_singular() ) {
			return;
		}

		$links = $this->get_links();
		if ( isset( $links['next'] ) ) {
			echo '<link rel="next" href="', esc_url( $links['next'] ), '">', "\n";
		}
		if ( isset( $links['prev'] ) ) {
			echo '<link rel="prev" href="', esc_url( $links['prev'] ), '">', "\n";
		}
	}

	private function get_links(): array {
		$url   = $this->get_value();
		$links = [];

		global $wp_query;
		$per_page = apply_filters('slim_seo_per_page', get_query_var('posts_per_page') );
		$wp_query->set('posts_per_page', $per_page );

		$paged = max( 1, get_query_var( 'paged' ) );
		if ( $paged > 1 ) {
			$links['prev'] = $this->build_link( $url, $paged - 1 );
		}
		if ( $paged < $wp_query->max_num_pages ) {
			$links['next'] = $this->build_link( $url, $paged + 1 );
		}

		return $links;
	}

	private function build_link( string $url, int $paged ): string {
		if ( get_option( 'permalink_structure' ) ) {
			return $paged === 1 ? $url : trailingslashit( $url ) . 'page/' . user_trailingslashit( $paged, 'paged' );
		}

		return add_query_arg( 'paged', $paged, $url );
	}

	private function get_home_value(): string {
		return home_url( '/' );
	}

	private function get_singular_value(): string {
		return get_permalink( $this->get_queried_object() );
	}

	private function get_term_value(): string {
		$term_link = get_term_link( $this->get_queried_object() );
		return is_string( $term_link ) ? $term_link : '';
	}

	private function get_post_type_archive_value(): string {
		return get_post_type_archive_link( $this->get_queried_object()->name );
	}

	private function get_author_value(): string {
		return get_author_posts_url( $this->get_queried_object_id() );
	}
}
