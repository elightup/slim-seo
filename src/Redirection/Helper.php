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

	public static function normalize_url( string $url, $unslash = true ) : string {
		$url = $unslash ? wp_unslash( $url ) : $url;
		$url = sanitize_text_field( $url );
		$url = html_entity_decode( $url );
		$url = str_replace( untrailingslashit( home_url() ), '', $url );
		$url = rtrim( $url, '/' );
		$url = ltrim( $url, '/' );

		return $url ? $url : '/';
	}

	public static function get_children_posts( int $post_id ) : array {
		$posts          = [];
		$children_posts = get_children( [ 'post_parent' => $post_id ] );

		if ( empty( $children_posts ) ) {
			return $posts;
		}

		foreach ( $children_posts as $children_post ) {
			$posts[] = $children_post;

			$child_children_posts = self::get_children_posts( $children_post->ID );

			if ( ! empty( $child_children_posts ) ) {
				$posts = array_merge( $posts, $child_children_posts );
			}
		}

		return $posts;
	}

	public static function save_old_permalink( int $post_id, string $permalink, string $permalink_before ) {
		$old_permalinks = (array) get_post_meta( $post_id, '_ss_old_permalink' );

		if ( ! in_array( $permalink_before, $old_permalinks, true ) ) {
			add_post_meta( $post_id, '_ss_old_permalink', $permalink_before );
		}

		if ( in_array( $permalink, $old_permalinks, true ) ) {
			delete_post_meta( $post_id, '_ss_old_permalink', $permalink );
		}
	}
}
