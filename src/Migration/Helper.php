<?php
namespace SlimSEO\Migration;

class Helper {
	public static function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );
		return array_keys( $post_types );
	}

	public static function get_terms() {
		$taxonomies = get_taxonomies( [
			'public' => true,
		] );
		unset( $taxonomies['post_format'] );
		$taxonomies = array_keys( $taxonomies );
		$terms = get_terms( [
			'taxonomy'   => $taxonomies,
			'hide_empty' => false,
			'fields'     => 'ids',
		] );
		return $terms;
	}

	public static function get_platforms() {
		return [
			'yoast'         => __( 'Yoast SEO', 'slim-seo' ),
			'aioseo'        => __( 'All-In-One SEO Pack', 'slim-seo' ),
			'rank-math'     => __( 'Rank Math', 'slim-seo' ),
			'seopress'      => __( 'SEOPress', 'slim-seo' ),
			'seo-framework' => __( 'The SEO Framework', 'slim-seo' ),
		];
	}
}
