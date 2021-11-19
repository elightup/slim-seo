<?php
namespace SlimSEO\Migration;

class Helper {
	public static function get_post_types() {
		$post_types = get_post_types( array( 'public' => true ) );
		unset( $post_types['attachment'] );
		return array_keys( $post_types );
	}

	public static function get_taxonomies() {
		$taxonomies = get_taxonomies( array( 'public' => true ) );
		unset( $taxonomies['post_format'] );
		$taxonomies = array_keys( $taxonomies );
		return $taxonomies;
	}

	public static function get_platforms() {
		return array(
			'yoast'         => __( 'Yoast SEO', 'slim-seo' ),
			'aioseo'        => __( 'All In One SEO', 'slim-seo' ),
			'seo-framework' => __( 'The SEO Framework', 'slim-seo' ),
			'rank-math'     => __( 'Rank Math', 'slim-seo' ),
			'seopress'      => __( 'SEOPress', 'slim-seo' ),
		);
	}
}
