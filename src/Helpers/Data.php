<?php
namespace SlimSEO\Helpers;

class Data {
	public static function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		unset( $post_types['attachment'] );
		return apply_filters( 'slim_seo_post_types', $post_types );
	}

	public static function get_taxonomies() {
		$taxonomies = get_taxonomies( [
			'public'  => true,
			'show_ui' => true,
		], 'objects' );
		return apply_filters( 'slim_seo_taxonomies', $taxonomies );
	}

	public static function get_post_type_archive_page( string $post_type ) {
		$post_type_object = get_post_type_object( $post_type );
		if ( ! $post_type_object || ! is_string( $post_type_object->has_archive ) ) {
			return null;
		}

		$page = get_page_by_path( $post_type_object->has_archive );
		return $page ?: null;
	}

	public static function get_migration_sources( string $type = '' ): array {
		$sources = [
			'meta'        => [
				'yoast'         => __( 'Yoast SEO', 'slim-seo' ),
				'aioseo'        => __( 'All In One SEO', 'slim-seo' ),
				'seo-framework' => __( 'The SEO Framework', 'slim-seo' ),
				'rank-math'     => __( 'Rank Math', 'slim-seo' ),
				'seopress'      => __( 'SEOPress', 'slim-seo' ),
				'squirrly'      => __( 'Squirrly SEO', 'slim-seo' ),
			],
			'redirection' => [
				'redirection'   => _x( 'Redirection', 'Plugin Name', 'slim-seo' ),
				'301-redirects' => _x( '301 Redirects', 'Plugin Name', 'slim-seo' ),
			],
		];

		return $type ? $sources[ $type ] : array_merge( $sources['meta'], $sources['redirection'] );
	}

	public static function get_posts( array $args = [] ): array {
		$posts = get_posts( array_merge( [
			'post_type'      => array_keys( self::get_post_types() ),
			'post_status'    => [ 'publish' ],
			'posts_per_page' => -1,
		], $args ) );

		return $posts;
	}
}
