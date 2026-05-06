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
		add_filter( 'slim_seo_allowed_blocks', [ $this, 'allowed_blocks' ] );
	}

	public function filter_content( string $post_content, WP_Post $post ): string {
		return $this->get_builder_content( $post ) ?? $post_content;
	}

	private function get_builder_content( WP_Post $post ): ?string {
		if ( ! $this->build_with_divi( $post->ID ) ) {
			return null;
		}

		$content = $post->post_content;

		if ( ! $this->is_divi_5( $post->ID ) ) {
			$content = $this->remove_shortcodes( $content );
		}

		return $content;
	}

	private function build_with_divi( int $post_id ): bool {
		return get_post_meta( $post_id, '_et_pb_use_builder', true );
	}

	private function is_divi_5( int $post_id ): bool {
		return get_post_meta( $post_id, '_et_pb_use_divi_5', true );
	}

	private function remove_shortcodes( string $content ): string {
		return preg_replace( '~\[/?[^\]]+?/?\]~s', '', $content );
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
			'layout_category',
		];
		return array_diff_key( $taxonomies, array_flip( $unsupported ) );
	}

	public function allowed_blocks( array $blocks ): array {
		return array_merge( $blocks, [ 'divi/placeholder', 'divi/text', 'divi/section', 'divi/row', 'divi/column' ] );
	}
}
