<?php
namespace SlimSEO;

class Helper {
	public static function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );
		return array_keys( $post_types );
	}

	public static function get_migration_platforms() {
		$default = [
			'yoast'             => __( 'Yoast SEO', 'slim-seo' ),
			'aioseo'            => __( 'All In One SEO Package', 'slim-seo' ),
			'rank-math'         => __( 'Rank Math', 'slim-seo' ),
			'SEOPress'          => __( 'SEOPress', 'slim-seo' ),
			'the-seo-framework' => __( 'The SEO Framework', 'slim-seo' ),
		];
		return apply_filters( 'slim_seo_migration_platforms', $default );
	}
}
