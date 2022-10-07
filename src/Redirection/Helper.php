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

		return $settings[ $name ] ?? false;
	}
}
