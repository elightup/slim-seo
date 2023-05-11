<?php
namespace SlimSEO\MetaTags;

class Helper {
	public static function normalize( $text ) {
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
			'mailpoet_form',
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

		// Remove extra white spaces.
		$text = preg_replace( '/\s+/', ' ', $text );
		$text = trim( $text );

		return $text;
	}

	public static function maybe_skip_block( ?string $output, array $block ): ?string {
		$skipped_blocks = apply_filters( 'slim_seo_skipped_blocks', [
			'fluentfom/guten-block',
			'wpforms/form-selector',
			'mailpoet/subscription-form-block',
		] );
		return in_array( $block['blockName'], $skipped_blocks, true ) ? '' : $output;
	}
}
