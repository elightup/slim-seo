<?php
namespace SlimSEO\Integrations;

use WP_Post;

class Divi {
	public function is_active(): bool {
		return defined( 'ET_BUILDER_VERSION' );
	}

	public function setup() {
		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
	}

	public function description( $description, WP_Post $post ) {
		// If the post is built with Divi, then strips all shortcodes, but keep the content.
		if ( get_post_meta( $post->ID, '_et_builder_version', true ) ) {
			$description = preg_replace( '~\[/?[^\]]+?/?\]~s', '', $post->post_content );
		}
		return $description;
	}

	public function remove_post_types( $post_types ) {
		unset( $post_types['et_pb_layout'] );
		return $post_types;
	}
}
