<?php
namespace SlimSEO\Integrations;

class Elementor {
	public function setup() {
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

		add_filter( 'slim_seo_no_post_content', [ $this, 'no_post_content' ], 1, 2 );
	}

	public function remove_post_types( $post_types ) {
		$unsupported = [
			'elementor_library',
			'e-floating-buttons',
			'e-landing-page',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}

	public function no_post_content( bool $skip, int $post_id ): bool {
		$post = get_post( $post_id );
		if ( empty( $post ) ) {
			return true;
		}

		$post_content = $post->post_content;
		$skipped_page = [
			'elementor-template',
			'elementor_login',
			'elementor_register',
		];
		return array_walk( $skipped_page, function ( $page ) use ( $post_content ) {
			return str_contains( $post_content, $page );
		} );
	}
}
