<?php
namespace SlimSEO\Migration;

class Helper {
	public static function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );
		return array_keys( $post_types );
	}

	public static function get_taxonomies() {
		$taxonomies = get_taxonomies( [
			'public' => true,
		] );
		unset( $taxonomies['post_format'] );
		$taxonomies = array_keys( $taxonomies );
		return $taxonomies;
	}

	public static function get_platforms( string $type = '' ) : array {
		$platforms = [
			'meta'        => [
				'yoast'         => __( 'Yoast SEO', 'slim-seo' ),
				'aioseo'        => __( 'All In One SEO', 'slim-seo' ),
				'seo-framework' => __( 'The SEO Framework', 'slim-seo' ),
				'rank-math'     => __( 'Rank Math', 'slim-seo' ),
				'seopress'      => __( 'SEOPress', 'slim-seo' ),
			],
			'redirection' => [
				'redirection'   => _x( 'Redirection', 'Plugin Name', 'slim-seo' ),
				'301-redirects' => _x( '301 Redirects', 'Plugin Name', 'slim-seo' ),
			],
		];

		if ( '' !== $type ) {
			return $platforms[ $type ];
		}

		return array_merge( $platforms['meta'], $platforms['redirection'] );
	}
}
