<?php
namespace SlimSEO\Helpers;

use WP_Post;

class Videos {
	private static $doc;
	private static $id;

	public static function get_post_videos( $post ): array {
		if ( ! ( $post instanceof WP_Post ) ) {
			return [];
		}

		return self::get_videos_from_html( $post->post_content );
	}

	private static function get_videos_from_html( string $html ): array {
		self::prepare_dom();
		if ( empty( self::$doc ) ) {
			return [];
		}

		$html = '<?xml encoding="' . get_bloginfo( 'charset' ) . '"?>' . $html;
		self::$doc->loadHTML( $html );

		// Clear the errors to clean up the memory.
		libxml_clear_errors();

		/**
		 * Get only in video tag
		 */
		$videos = [];
		foreach ( self::$doc->getElementsByTagName( 'video' ) as $video ) {
			$src = '';
			$src = $video->getAttribute( 'src' );

			if ( empty( $src ) ) {
				continue;
			}

			$poster = $video->getAttribute( 'poster' );

			$videos[] = [
				'content_loc' => self::absolute_url( $src ),
				'thumbnail'   => $poster ? self::absolute_url( $poster ) : '',
			];
		}

		return $videos;
	}

	private static function prepare_dom(): void {
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

	private static function absolute_url( string $url ): string {
		if ( wp_parse_url( $url, PHP_URL_SCHEME ) ) {
			return $url;
		}

		$home = wp_parse_url( home_url() );

		if ( str_starts_with( $url, '//' ) ) {
			return "{$home['scheme']}:{$url}";
		}

		return "{$home['scheme']}://{$home['host']}/" . ltrim( $url, '/' );
	}
}
