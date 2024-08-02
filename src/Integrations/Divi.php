<?php
namespace SlimSEO\Integrations;

use WP_Post;
use SlimSEO\Helpers\Arr;

class Divi {
	private $is_auto_genereted = false;

	public function is_active(): bool {
		return defined( 'ET_BUILDER_VERSION' );
	}

	public function setup() {
		add_filter( 'slim_seo_data', [ $this, 'replace_post_content' ] );
		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
	}

	public function replace_post_content( array $data ): array {
		if ( $this->is_auto_genereted ) {
			return $data;
		}

		$post = is_singular() ? get_queried_object() : get_post();
		if ( empty( $post ) ) {
			return $data;
		}

		$content = $this->get_post_content( $post );
		if ( $content ) {
			Arr::set( $data, 'post.content', $content );
		}

		return $data;
	}

	public function description( $description, WP_Post $post ) {
		$content = $this->get_post_content( $post );
		if ( $content ) {
			$this->is_auto_genereted = true;
			return $content;
		}

		return $description;
	}

	public function get_post_content( WP_Post $post ):string {
		// If the post is built with Divi, then strips all shortcodes, but keep the content.
		if ( get_post_meta( $post->ID, '_et_builder_version', true ) ) {
			return preg_replace( '~\[/?[^\]]+?/?\]~s', '', $post->post_content );
		}

		return '';
	}

	public function remove_post_types( $post_types ) {
		unset( $post_types['et_pb_layout'] );
		return $post_types;
	}
}
