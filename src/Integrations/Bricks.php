<?php
namespace SlimSEO\Integrations;

use SlimTwig\Data;
use WP_Post;
use SlimSEO\MetaTags\Helper;

class Bricks {

	private $skipped_elements = [
		// Bricks.
		'code',
		'divider',
		'facebook-page',
		'form',
		'icon',
		'image',
		'map',
		'pagination',
		'pie-chart',
		'post-author',
		'post-comments',
		'post-meta',
		'post-navigation',
		'post-taxonomy',
		'post-sharing',
		'post-title',
		'related-posts',
		'social-icons',
		'video',

		// Extra elements.
		'wpgb-facet',
		'jet-engine-listing-grid',
		'happyfiles-gallery',
	];

	public function is_active(): bool {
		return defined( 'BRICKS_VERSION' );
	}

	public function setup(): void {
		$this->skipped_elements = apply_filters( 'slim_seo_bricks_skipped_elements', $this->skipped_elements );

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
		// Get from the post only, don't get from the template.
		$data = get_post_meta( $post->ID, BRICKS_DB_PAGE_CONTENT, true );
		if ( empty( $data ) ) {
			return null;
		}

		$data = array_filter( $data, [ $this, 'should_render' ] );

		// Skip shortcodes & blocks inside dynamic data {post_content}.
		add_filter( 'the_content', [ $this, 'skip_shortcodes' ], 5 );

		add_filter( 'bricks/element/render', [ $this, 'skip_render_element' ], 10, 2 );

		$content = \Bricks\Frontend::render_data( $data );

		remove_filter( 'bricks/element/render', [ $this, 'skip_render_element' ], 10, 2 );

		// Remove the filter.
		remove_filter( 'the_content', [ $this, 'skip_shortcodes' ], 5 );

		return (string) $content;
	}

	public function skip_render_element( $render_element, $element ): bool {
		if ( ! $render_element ) {
			return $render_element;
		}

		// Ignore nested loop. In this case $render_element is an array of loop IDs.
		if ( is_array( $render_element ) ) {
			return false;
		}

		return $this->should_render( $element ) ? $render_element : false;
	}

	private function should_render( $element ): bool {
		$element_data = \Bricks\Elements::get_element( (array) $element );

		// Ignore all elements in certain categories.
		if ( in_array( Data::get( $element_data, 'category' ), [ 'media', 'query', 'wordpress', 'extras' ], true ) ) {
			return false;
		}

		if ( in_array( Data::get( $element, 'name' ), $this->skipped_elements ) ) {
			return false;
		}

		// Ignore element with query loop.
		if ( Data::get( $element, 'settings.hasLoop' ) ) {
			return false;
		}

		// Ignore popups.
		if ( $this->is_popup( $element ) ) {
			return false;
		}

		// Ignore components.
		if ( Data::get( $element, 'cid' ) ) {
			return false;
		}

		// Remove elements with scripts, like sliders or counters, to avoid breaking layouts.
		// Don't count 'bricksBackgroundVideoInit' as it's always enabled for nestable elements.
		$scripts = (array) Data::get( $element_data, 'scripts', [] );
		$scripts = array_diff( $scripts, [ 'bricksBackgroundVideoInit' ] );
		if ( ! empty( $scripts ) ) {
			return false;
		}

		return true;
	}

	private function is_popup( $element ): bool {
		if ( Data::get( $element, 'name' ) !== 'template' ) {
			return false;
		}

		$template_id = intval( Data::get( $element, 'settings.template' ) );
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
