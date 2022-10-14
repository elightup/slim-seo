<?php
namespace SlimSEO\Redirection;

class Helper {
	public static function redirect_types() : array {
		return [
			301 => __( '301 Moved Permanently', 'slim-seo' ),
			302 => __( '302 Found', 'slim-seo' ),
			307 => __( '307 Temporary Redirect', 'slim-seo' ),
			410 => __( '410 Content Deleted', 'slim-seo' ),
			451 => __( '451 Unavailable For Legal Reasons', 'slim-seo' ),
		];
	}

	public static function condition_options() : array {
		return [
			'exact-match' => __( 'Exact Match', 'slim-seo' ),
			'contain'     => __( 'Contain', 'slim-seo' ),
			'start-with'  => __( 'Start With', 'slim-seo' ),
			'end-with'    => __( 'End With', 'slim-seo' ),
			'regex'       => __( 'Regex', 'slim-seo' ),
		];
	}

	/**
	 * Check if the current request is HTTPS
	 *
	 * @link https://developer.wordpress.org/reference/functions/is_ssl/
	 */
	public static function is_ssl() : bool {
		// Cloudflare
		if ( ! empty( $_SERVER['HTTP_CF_VISITOR'] ) ) {
			$cfo = json_decode( $_SERVER['HTTP_CF_VISITOR'] ); // @codingStandardsIgnoreLine.
			if ( isset( $cfo->scheme ) && 'https' === $cfo->scheme ) {
				return true;
			}
		}

		// Other proxy
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
			return true;
		}

		return is_ssl();
	}

	public static function normalize_url( string $url ) : string {
		$home_url = untrailingslashit( home_url() );
		$url      = wp_unslash( $url );
		$url      = sanitize_text_field( $url );
		$url      = html_entity_decode( $url );
		$url      = str_replace( $home_url, '', $url );
		$url      = rtrim( $url, '/' );
		return $url;
	}
}
