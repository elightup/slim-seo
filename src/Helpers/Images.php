<?php
namespace SlimSEO\Helpers;

use WP_Post;

class Images {
	public $doc;
	public static function get_post_images( WP_Post $post ): array {
		$images = [];

		// Post thumbnail.
		$images[] = get_post_thumbnail_id( $post );

		// Get images from post content.
		$images = array_merge( $images, self::get_images_from_html( $post->post_content ) );

		return array_filter( $images );
	}

	public static function get_images_from_html( string $html ): array {
		self::prepare_dom();
		if ( empty( $doc ) ) {
			return [];
		}

		// Set encoding.
		$html = '<?xml encoding="' . get_bloginfo( 'charset' ) . '"?>' . $html;

		$doc->loadHTML( $html );

		// Clear the errors to clean up the memory.
		libxml_clear_errors();

		$values = [];
		$images = $doc->getElementsByTagName( 'img' );
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

	public static function prepare_dom() {
		// Use DOMDocument instead of SimpleXML to load non-well-formed HTML.
		if ( ! class_exists( 'DOMDocument' ) ) {
			return;
		}

		// Do not generate a notice when there's an error.
		libxml_use_internal_errors( true );

		if ( empty( $doc ) ) {
			$doc = new \DOMDocument();
		}
	}
}