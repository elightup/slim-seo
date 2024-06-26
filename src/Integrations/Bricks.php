<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\Helper;
use WP_Post;

class Bricks {
	public function is_active(): bool {
		return defined( 'BRICKS_VERSION' );
	}

	public function setup() {
		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );

		add_filter( 'bricks/frontend/disable_opengraph', '__return_true' );
		add_filter( 'bricks/frontend/disable_seo', '__return_true' );

		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
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

		$data = $this->remove_elements( $data );

		// Skip shortcodes & blocks inside dynamic data {post_content}.
		add_filter( 'the_content', [ $this, 'skip_shortcodes' ], 5 );

		$description = \Bricks\Frontend::render_data( $data );

		// Remove the filter.
		remove_filter( 'the_content', [ $this, 'skip_shortcodes' ], 5 );

		return (string) $description;
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

			return true;
		} );
	}

	public function remove_post_types( array $post_types ): array {
		unset( $post_types['bricks_template'] );
		return $post_types;
	}

	public function skip_shortcodes( string $content ): string {
		return Helper::normalize( $content );
	}
}
