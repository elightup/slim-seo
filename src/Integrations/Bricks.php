<?php
namespace SlimSEO\Integrations;

use WP_Post;

class Bricks {
	public function setup() {
		if ( ! defined( 'BRICKS_VERSION' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );

		add_filter( 'bricks/frontend/disable_opengraph', '__return_true' );
		add_filter( 'bricks/frontend/disable_seo', '__return_true' );

		add_filter( 'slim_seo_sitemap_post_types', [ $this, 'exclude_templates' ] );
	}

	public function description( $description, WP_Post $post ) {
		// Get from the post first, then from the template.
		$data = get_post_meta( $post->ID, BRICKS_DB_PAGE_CONTENT, true );
		if ( empty( $data ) ) {
			$data = \Bricks\Helpers::get_bricks_data( $post->ID );
		}
		if ( empty( $data ) ) {
			return $description;
		}

		$data        = $this->remove_elements( $data );
		$description = \Bricks\Frontend::render_data( $data );

		return $description;
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
			'social-icons',
			'svg',
			'video',
			'wordpress',

			// WP Grid Builder.
			'wpgb-facet',
		] );

		return array_filter( $data, function( $element ) use ( $skipped_elements ) {
			if ( in_array( $element['name'], $skipped_elements, true ) ) {
				return false;
			}

			// Ignore element with query loop.
			if ( ! empty( $element['settings']['hasLoop'] ) ) {
				return false;
			}

			return true;
		} );
	}

	public function exclude_templates( array $post_types ): array {
		unset( $post_types['bricks_template'] );
		return $post_types;
	}
}
