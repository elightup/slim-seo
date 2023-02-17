<?php
namespace SlimSEO\Integrations;

use WP_Post;

class Divi {
	public function setup() {
		if ( ! defined( 'ET_BUILDER_VERSION' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );
	}

	public function description( $description, WP_Post $post ) {
		// If the post is built with Divi, then strips all shortcodes, but keep the content.
		if ( get_post_meta( $post->ID, '_et_builder_version', true ) ) {
			$description = preg_replace( '~\[/?[^\]]+?/?\]~s', '', $post->post_content );
		}
		return $description;
	}
}
