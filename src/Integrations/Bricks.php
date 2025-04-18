<?php
namespace SlimSEO\Integrations;

use WP_Post;
use SlimSEO\MetaTags\Helper;
use Bricks\Element;

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
			return null;
		}

		// Skip shortcodes & blocks inside dynamic data {post_content}.
		add_filter( 'the_content', [ $this, 'skip_shortcodes' ], 5 );

		add_filter( 'bricks/element/render', [ $this, 'skip_render_element' ], 10, 2 );

		$content = \Bricks\Frontend::render_data( $data );

		remove_filter( 'bricks/element/render', [ $this, 'skip_render_element' ], 10, 2 );

		// Remove the filter.
		remove_filter( 'the_content', [ $this, 'skip_shortcodes' ], 5 );

		return (string) $content;
	}

	public function skip_render_element( $render_element, Element $element ): bool {
		if ( ! $render_element ) {
			return $render_element;
		}

		// Ignore nested loop. In this case $render_element is an array of loop IDs.
		if ( is_array( $render_element ) ) {
			return false;
		}

		// Skip these elements as their content are not suitable for meta description.
		$skipped_elements = apply_filters( 'slim_seo_bricks_skipped_elements', [
			// Bricks.
			'audio',
			'code',
			'divider',
			'facebook-page',
			'form',
			'icon',
			'image',
			'image-gallery',
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

			// HappyFiles.
			'happyfiles-gallery',
		] );

		if ( in_array( $element->name, $skipped_elements ) ) {
			return false;
		}

		// Ignore element with query loop.
		if ( ! empty( $element->settings['hasLoop'] ) ) {
			return false;
		}

		// Ignore popups.
		if ( $this->is_popup( $element ) ) {
			return false;
		}

		// Ignore components.
		if ( $element->cid ) {
			return false;
		}

		// Remove elements with scripts, like sliders or counters, to avoid breaking layouts.
		// Don't count 'bricksBackgroundVideoInit' as it's always enabled for nestable elements.
		$scripts = array_diff( $element->scripts, [ 'bricksBackgroundVideoInit' ] );
		if ( ! empty( $scripts ) ) {
			return false;
		}

		return $render_element;
	}

	private function is_popup( Element $element ): bool {
		if ( $element->name !== 'template' ) {
			return false;
		}

		$template_id = isset( $element->settings['template'] ) ? intval( $element->settings['template'] ) : 0;
		if ( ! $template_id ) {
			return false;
		}

		$template_type = \Bricks\Templates::get_template_type( $template_id );
		return $template_type === 'popup';
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
