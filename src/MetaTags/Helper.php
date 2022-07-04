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
			'happyforms',               // HappyForms.
			'contact',                  // Very Simple Contact Form.
			'edd_invoices',             // EDD Invoices.
			'velocity',                 // Velocity.
			'fluentform',               // Fluent Forms

			'rwmb_meta',                // Meta Box.
			'mb_frontend_form',         // MB Frontend Submission.
			'mb_frontend_dashboard',
			'mb_user_profile_register', // MB User Profile.
			'mb_user_profile_login',
			'mb_user_profile_info',
			'mb_relationships',         // MB Relationships.
			'mbfp-button',              // MB Favorite Posts.

			/**
			 * Divi
			 *
			 * @link https://www.elegantthemes.com/gallery/divi/
			 */
			'et_pb_column',
			'et_pb_fullwidth_section',
			'et_pb_row',
			'et_pb_section',

			'et_pb_button',
			'et_pb_code',
			'et_pb_fullwidth_code',
			'et_pb_gallery',
			'et_pb_image',
			'et_pb_slide',
			'et_pb_slider',
			'et_pb_text',
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
			$text = do_blocks( $text );
		}

		// Replace HTML tags with spaces.
		$text = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );
		$text = preg_replace( '@<[^>]*?>@s', ' ', $text );

		// Remove extra white spaces.
		$text = preg_replace( '/\s+/', ' ', $text );
		$text = trim( $text );

		return $text;
	}
}
