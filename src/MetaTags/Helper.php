<?php
namespace SlimSEO\MetaTags;

class Helper {
	public static function normalize( $text ) {
		$text = do_shortcode( $text );               // Parse shortcodes. Works with posts that have shortcodes in the content (using page builders like Divi).
		$text = wp_strip_all_tags( $text );          // No HTML tags.
		$text = preg_replace( '/\s+/', ' ', $text ); // Remove extra white spaces.
		$text = trim( $text );

		return $text;
	}
}
