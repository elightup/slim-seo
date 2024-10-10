<?php
namespace SlimSEO\MetaTags;

use SlimTwig\Renderer;
use SlimSEO\Helpers\Arr;

class Helper {
	public static function normalize( $text ): string {
		global $shortcode_tags;

		/**
		 * Some shortcodes (like HappyForms) inject <link> or other assets to the page.
		 * do_shortcode here will parse the <link> and then remove it, which might break the style/JS.
		 */
		$skipped_shortcodes = apply_filters( 'slim_seo_skipped_shortcodes', [
			'happyforms',
			'contact',      // Very Simple Contact Form.
			'edd_invoices',
			'velocity',
			'fluentform',
			'wpforms',
			'ninja_form',
			'mailpoet_form',
			'gravityview',
			'civicrm',
			'contact-form-7',

			// Filter everything.
			'fe_widget',
			'fe_chips',
			'fe_sort',
		] );

		$shortcodes_bak = $shortcode_tags;

		// @codingStandardsIgnoreLine.
		$shortcode_tags = array_diff_key( $shortcode_tags, array_flip( $skipped_shortcodes ) );
		$text           = do_shortcode( $text );      // Parse shortcodes. Works with posts that have shortcodes in the content (using page builders like Divi).

		// @codingStandardsIgnoreLine.
		$shortcode_tags = $shortcodes_bak;            // Revert the global shortcodes registry.
		$text           = strip_shortcodes( $text );  // Strip all non-parsed shortcodes.

		// Render blocks.
		if ( function_exists( 'do_blocks' ) ) {
			add_filter( 'pre_render_block', [ __CLASS__, 'maybe_skip_block' ], 10, 2 );
			$text = do_blocks( $text );
			remove_filter( 'pre_render_block', [ __CLASS__, 'maybe_skip_block' ] );
		}

		// Replace HTML tags with spaces.
		$text = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );
		$text = preg_replace( '@<[^>]*?>@s', ' ', $text );

		// Remove lonely separator
		$separator = apply_filters( 'document_title_separator', '-' ); // phpcs:ignore
		$text      = trim( $text );
		$text      = trim( $text, $separator );

		// Remove extra white spaces.
		$text = preg_replace( '/\s+/', ' ', $text );
		$text = trim( $text );

		return $text;
	}

	public static function maybe_skip_block( ?string $output, array $block ): ?string {
		$skipped_blocks = apply_filters( 'slim_seo_skipped_blocks', [
			'core/query',
			'core/code',
			'fluentfom/guten-block',
			'wpforms/form-selector',
			'ninja-forms/form',
			'mailpoet/subscription-form-block',
		] );
		return in_array( $block['blockName'], $skipped_blocks, true ) ? '' : $output;
	}

	public static function get_taxonomies() {
		$unsupported = [
			'wp_theme',
			'wp_template_part_area',
			'wp_pattern_category',
			'link_category',
			'nav_menu',
			'post_format',
			'mb-views-category',
		];
		$taxonomies  = get_taxonomies( [], 'objects' );
		$taxonomies  = array_diff_key( $taxonomies, array_flip( $unsupported ) );
		$taxonomies  = array_map( function ( $taxonomy ) {
			return [
				'slug' => $taxonomy->name,
				'name' => $taxonomy->label,
			];
		}, $taxonomies );

		return array_values( $taxonomies );
	}

	public static function render( string $text, int $post_id = 0, int $term_id = 0, array $data = [] ): string {
		return str_contains( $text, '{{' ) ? self::render_dynamic_variables( $text, $post_id, $term_id, $data ) : $text;
	}

	private static function render_dynamic_variables( string $text, int $post_id = 0, int $term_id = 0, array $data = [] ): string {
		static $cache = [];

		$key = "{$post_id}:{$term_id}";
		if ( empty( $cache[ $key ] ) ) {
			$data_object = new Data;

			if ( $post_id ) {
				$data_object->set_post_id( $post_id );
			}
			if ( $term_id ) {
				$data_object->set_term_id( $term_id );
			}

			$cache[ $key ] = $data_object->collect();
		}

		$render_data = $cache[ $key ];

		if ( ! empty( $data ) ) {
			$render_data = Arr::merge_recursive( $render_data, $data );
		}

		$value = Renderer::render( $text, $render_data );
		$value = self::normalize( $value );

		return $value;
	}

	public static function truncate( string $text, int $max = 160 ): string {
		$text = self::normalize( $text );
		return mb_substr( $text, 0, $max );
	}
}
