<?php
namespace SlimSEO\Helpers;

use WP_Post;

class Images {
	private static $doc;
	private static $cache = [];

	public static function get_post_images( WP_Post $post ): array {
		$images = [];
		$data   = [];

		// Post thumbnail.
		$data[] = get_post_thumbnail_id( $post );

		// Get images from post content.
		$data = array_merge( $data, self::get_images_from_html( $post->post_content ) );
		foreach ( $data as $image ) {
			$images[] = self::normalize_image( $image );
		}

		return array_filter( $images );
	}

	public static function get_id_from_url( string $url ): int {
		if ( ! isset( self::$cache[ $url ] ) ) {
			self::$cache[ $url ] = attachment_url_to_postid( $url );
		}

		return self::$cache[ $url ];
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

	private static function normalize_image( $image ): string {
		// If we get image ID only.
		if ( is_numeric( $image ) ) {
			return get_attached_file( $image ) ? wp_get_attachment_image_url( $image, 'full' ) : '';
		}

		return self::get_absolute_url( $image );
	}

	private static function get_absolute_url( string $url ): string {
		if ( wp_parse_url( $url, PHP_URL_SCHEME ) ) {
			return $url;
		}

		$url_parts = wp_parse_url( home_url() );
		// Non-protocol URL.
		if ( str_starts_with( $url, '//' ) ) {
			return "{$url_parts[ 'scheme' ]}:{$url}";
		}
		if ( empty( $url_parts[ 'port' ] ) ) {
			return $url_parts[ 'scheme' ] . '://' . trailingslashit( $url_parts[ 'host' ] ) . ltrim( $url, '/' );
		}
		// Relative URL.
		return $url_parts[ 'scheme' ] . '://' . trailingslashit( $url_parts[ 'host' ] . ':' . $url_parts[ 'port' ] ) . ltrim( $url, '/' );
	}

}