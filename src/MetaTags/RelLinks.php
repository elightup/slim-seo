<?php
namespace SlimSEO\MetaTags;

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
			echo '<link rel="next" href="', esc_url( $links['next'] ), '" />', "\n";
		}
		if ( isset( $links['prev'] ) ) {
			echo '<link rel="prev" href="', esc_url( $links['prev'] ), '" />', "\n";
		}
	}

	private function get_links() {
		$url = $this->get_value();
		$links = [
			'prev' => null,
			'next' => null
		];

		global $wp_query;
		$paged = max( 1, get_query_var( 'paged' ) );
		if ( $paged > 1 ) {
			$links['prev'] = $this->build_link( $url, $paged - 1 );
		}
		if ( $paged < $wp_query->max_num_pages ) {
			$links['next'] = $this->build_link( $url, $paged + 1 );
		}

		return $links;
	}

	private function build_link( $url, $paged ) {
		if ( '' == get_option( 'permalink_structure' ) ) {
			$url = add_query_arg( 'paged', $paged, $url );
		} else {
			$url = trailingslashit( $url ) . 'page/' . user_trailingslashit( $paged, 'paged' );
		}
		return $url;
	}

	private function get_home_value() {
		return home_url( '/' );
	}

	private function get_singular_value() {
		return get_permalink( get_queried_object() );
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
}