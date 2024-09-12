<?php
namespace SlimSEO\Integrations;

class Elementor {
	public function setup() {
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );
	}

	public function remove_post_types( $post_types ) {
		$unsupported = [
			'elementor_library',
			'e-floating-buttons',
			'e-landing-page',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}
}
