<?php
namespace SlimSEO\Integrations;

class BeaverBuilder {
	public function is_active(): bool {
		return defined( 'FL_BUILDER_VERSION' );
	}

	public function setup(): void {
		add_filter( 'fl_builder_disable_schema', '__return_true' );
		add_filter( 'fl_theme_disable_schema', '__return_true' );

		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_taxonomies', [ $this, 'remove_taxonomies' ] );
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );

		add_filter( 'slim_seo_allowed_blocks', [ $this, 'allowed_blocks' ] );
	}

	public function remove_post_types( array $post_types ): array {
		unset( $post_types['fl-builder-template'] );
		return $post_types;
	}

	public function remove_taxonomies( array $taxonomies ): array {
		unset( $taxonomies['fl-builder-template-category'] );
		return $taxonomies;
	}

	public function skip_shortcodes( array $shortcodes ): array {
		$shortcodes[] = 'fl_builder_insert_layout';
		return $shortcodes;
	}

	public function allowed_blocks( array $blocks ): array {
		$blocks[] = 'fl-builder/layout';
		return $blocks;
	}
}
