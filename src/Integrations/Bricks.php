<?php
namespace SlimSEO\Integrations;

use WP_Post;
use SlimSEO\MetaTags\Helper;

class Bricks {
	public function is_active(): bool {
		return defined( 'BRICKS_VERSION' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_post_content', [ $this, 'filter_content' ], 10, 2 );

		add_filter( 'bricks/frontend/disable_opengraph', '__return_true' );
		add_filter( 'bricks/frontend/disable_seo', '__return_true' );

		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_taxonomies', [ $this, 'remove_taxonomies' ] );
	}

	public function filter_content( string $post_content, WP_Post $post ): string {
		return $this->get_builder_content( $post ) ?? $post_content;
	}

	private function get_builder_content( WP_Post $post ): ?string {
		// Get from the post first, then from the template.
		$data = get_post_meta( $post->ID, BRICKS_DB_PAGE_CONTENT, true );
		if ( empty( $data ) ) {
			$data = \Bricks\Helpers::get_bricks_data( $post->ID );
		}
		if ( empty( $data ) ) {
			return null;
		}

		$data = $this->remove_elements( $data );

		// Skip shortcodes & blocks inside dynamic data {post_content}.
		add_filter( 'the_content', [ $this, 'skip_shortcodes' ], 5 );

		$content = \Bricks\Frontend::render_data( $data );

		// Remove the filter.
		remove_filter( 'the_content', [ $this, 'skip_shortcodes' ], 5 );

		return (string) $content;
	}

	private function remove_elements( array $data ): array {
		// Skip these elements as their content are not suitable for meta description.
		$skipped_elements = apply_filters( 'slim_seo_bricks_skipped_elements', [
			// Bricks.
			'audio',
			'code',
			'divider',
			'facebook-page',
			'form',
			'icon',
			'map',
			'nav-menu',
			'pagination',
			'pie-chart',
			'post-author',
			'post-comments',
			'post-meta',
			'post-navigation',
			'post-taxonomy',
			'post-sharing',
			'post-title',
			'posts',
			'related-posts',
			'search',
			'sidebar',
			'shortcode',
			'social-icons',
			'svg',
			'video',
			'wordpress',

			// WP Grid Builder.
			'wpgb-facet',
			'jet-engine-listing-grid',
		] );

		return array_filter( $data, function ( $element ) use ( $skipped_elements ) {
			if ( in_array( $element['name'], $skipped_elements, true ) ) {
				return false;
			}

			// Ignore element with query loop.
			if ( ! empty( $element['settings']['hasLoop'] ) ) {
				return false;
			}

			// Remove elements with scripts, like sliders or counters, to avoid breaking layouts.
			$scripts = \Bricks\Elements::get_element( $element, 'scripts' );
			// Don't count 'bricksBackgroundVideoInit' as it's always enabled for nestable elements.
			$scripts = array_diff( $scripts, [ 'bricksBackgroundVideoInit' ] );
			if ( ! empty( $scripts ) ) {
				return false;
			}

			return true;
		} );
	}

	public function remove_post_types( array $post_types ): array {
		$unsupported = [
			'bricks_template',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}

	public function remove_taxonomies( array $taxonomies ): array {
		$unsupported = [
			'template_tag',
			'template_bundle',
		];
		return array_diff_key( $taxonomies, array_flip( $unsupported ) );
	}

	public function skip_shortcodes( string $content ): string {
		return Helper::normalize( $content );
	}
}
