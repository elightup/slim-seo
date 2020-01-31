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
			'happyforms', // HappyForms
			'contact',    // Very Simple Contact Form
		] );
		$shortcodes_bak = $shortcode_tags;
		$shortcode_tags = array_diff_key( $shortcode_tags, array_flip( $skipped_shortcodes ) );

		$text = do_shortcode( $text );               // Parse shortcodes. Works with posts that have shortcodes in the content (using page builders like Divi).

		$shortcode_tags = $shortcodes_bak;           // Revert the global shortcodes registry.
		$text           = strip_shortcodes( $text ); // Strip all non-parsed shortcodes.

		$text = wp_strip_all_tags( $text );          // No HTML tags.
		$text = preg_replace( '/\s+/', ' ', $text ); // Remove extra white spaces.
		$text = trim( $text );

		return $text;
	}
}
