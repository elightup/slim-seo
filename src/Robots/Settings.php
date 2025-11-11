<?php
namespace SlimSEO\Robots;

use SlimSEO\Helpers\Assets;

class Settings {
	public function __construct() {
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_option', [ $this, 'save' ], 10, 2 );
	}

	public function enqueue(): void {
		Assets::enqueue_build_js( 'robots', 'SSRobots', [
			'settingsName' => 'slim_seo',
			'settings'     => self::list(),
			'fileExists'   => file_exists( ABSPATH . 'robots.txt' ),
		] );
	}

	public function save( array $option, array $data ): array {
		$option['robots_txt_editable'] = empty( $data['robots_txt_editable'] ) ? 0 : 1;

		return $option;
	}

	public static function list(): array {
		$saved_settings = get_option( 'slim_seo' ) ?: [];
		$settings       = [
			'robots_txt_editable' => 0,
			'robots_txt_content'  => '',
		];

		foreach ( $settings as $setting_name => $setting_value ) {
			if ( ! isset( $saved_settings[ $setting_name ] ) ) {
				continue;
			}

			$settings[ $setting_name ] = -1 !== $saved_settings[ $setting_name ] ? $saved_settings[ $setting_name ] : 0;
		}

		return $settings;
	}

	public static function get( string $name ) {
		$settings = self::list();

		return $settings[ $name ] ?? false;
	}

	public static function migrate( string $data ): bool {
		if ( empty( $data ) ) {
			return false;
		}

		$option                        = get_option( 'slim_seo', [] );
		$option['robots_txt_editable'] = 1;
		$option['robots_txt_content']  = $data;

		return update_option( 'slim_seo', $option );
	}
}
