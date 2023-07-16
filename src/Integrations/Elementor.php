<?php
namespace SlimSEO\Integrations;

class Elementor {
	public function setup() {
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );
	}

	public function remove_post_types( $post_types ) {
		unset( $post_types['elementor_library'] );
		unset( $post_types['e-landing-page'] );
		return $post_types;
	}
}
