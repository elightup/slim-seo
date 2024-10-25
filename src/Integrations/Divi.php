<?php
namespace SlimSEO\Integrations;

use WP_Post;

class Divi {
	public function is_active(): bool {
		return defined( 'ET_BUILDER_VERSION' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_taxonomies', [ $this, 'remove_taxonomies' ] );
		add_filter( 'slim_seo_post_content', [ $this, 'filter_content' ], 10, 2 );
	}

	public function filter_content( string $post_content, WP_Post $post ): string {
		return $this->get_builder_content( $post ) ?? $post_content;
	}

	private function get_builder_content( WP_Post $post ): ?string {
		// If the post is built with Divi, then strips all shortcodes, but keep the content.
		if ( get_post_meta( $post->ID, '_et_builder_version', true ) ) {
			return preg_replace( '~\[/?[^\]]+?/?\]~s', '', $post->post_content );
		}

		return null;
	}

	public function remove_post_types( array $post_types ): array {
		$unsupported = [
			'et_pb_layout',
			'project',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}

	public function remove_taxonomies( array $taxonomies ): array {
		$unsupported = [
			'project_category',
			'project_tag',
		];
		return array_diff_key( $taxonomies, array_flip( $unsupported ) );
	}
}
