<?php
namespace SlimSEO\Migration;

class Helper {
	public static function get_sources( string $type = '' ): array {
		$sources = [
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

		return $type ? $sources[ $type ] : array_merge( $sources['meta'], $sources['redirection'] );
	}
}
