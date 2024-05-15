<?php
namespace SlimSEO\Helpers;

use WP_Post;

class Images {
	private static $doc;

	public static function get_post_images( WP_Post $post ): array {
		$images = [];

		// Post thumbnail.
		$images[] = get_post_thumbnail_id( $post );

		// Get images from post content.
		$images = array_merge( $images, self::get_images_from_html( $post->post_content ) );

		return array_filter( $images );
	}

	private static function get_images_from_html( string $html ): array {
		self::prepare_dom();
		if ( empty( self::$doc ) ) {
			return [];
		}

		// Set encoding.
		$html = '<?xml encoding="' . get_bloginfo( 'charset' ) . '"?>' . $html;

		self::$doc->loadHTML( $html );

		// Clear the errors to clean up the memory.
		libxml_clear_errors();

		$values = [];
		$images = self::$doc->getElementsByTagName( 'img' );
		foreach ( $images as $image ) {
			$src = $image->getAttribute( 'src' );
			if ( empty( $src ) ) {
				continue;
			}

			$class = $image->getAttribute( 'class' );

			// Uploaded images.
			if ( preg_match( '/wp-image-(\d+)/', $class, $matches ) ) {
				$values[] = (int) $matches[ 1 ];
				continue;
			}

			$values[] = $src;
		}

		return $values;
	}

	private static function prepare_dom() {
		// Use DOMDocument instead of SimpleXML to load non-well-formed HTML.
		if ( ! class_exists( 'DOMDocument' ) ) {
			return;
		}

		// Do not generate a notice when there's an error.
		libxml_use_internal_errors( true );

		if ( empty( self::$doc ) ) {
			self::$doc = new \DOMDocument();
		}
	}
}