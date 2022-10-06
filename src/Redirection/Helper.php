<?php
namespace SlimSEO\Redirection;

class Helper {
	public static function redirect_types() : array {
		return [
			301 => __( '301 Moved Permanently', 'slim-seo-redirection' ),
			302 => __( '302 Found', 'slim-seo-redirection' ),
			307 => __( '307 Temporary Redirect', 'slim-seo-redirection' ),
			410 => __( '410 Content Deleted', 'slim-seo-redirection' ),
			451 => __( '451 Unavailable For Legal Reasons', 'slim-seo-redirection' ),
		];
	}

	public static function condition_options() : array {
		return [
			'exact-match' => __( 'Exact Match', 'slim-seo-redirection' ),
			'contain'     => __( 'Contain', 'slim-seo-redirection' ),
			'start-with'  => __( 'Start With', 'slim-seo-redirection' ),
			'end-with'    => __( 'End With', 'slim-seo-redirection' ),
			'regex'       => __( 'Regex', 'slim-seo-redirection' ),
		];
	}

	public static function get_settings() : array {
		return array_merge(
			[
				'enable_404_logs'     => 0,
				'redirect_404_to'     => '',
				'redirect_404_to_url' => '',
			],
			get_option( SLIM_SEO_REDIRECTION_SETTINGS_OPTION_NAME ) ?: [],
		);
	}

	public static function get_setting( string $name ) {
		$settings = self::get_settings();

		return $settings[$name] ?? false;
	}
}