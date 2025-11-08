<?php
namespace SlimSEO\Robots;

use SlimSEO\Helpers\Assets;

class Settings {
	public function __construct() {
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_option', [ $this, 'option_saved' ], 10, 2 );
	}

	public function enqueue(): void {
		Assets::enqueue_build_js( 'robots', 'SSRobots', [
			'settingsName' => 'slim_seo',
			'settings'     => self::list(),
			'fileExists'   => $this->file_exists(),
		] );
	}

	private function file_exists(): bool {
		return file_exists( ABSPATH . 'robots.txt' ) ? true : false;
	}

	public function option_saved( array $option, array $data ): array {
		$checkboxes = [
			'enable_edit_robots',
		];

		foreach ( $checkboxes as $checkbox ) {
			if ( empty( $data[ $checkbox ] ) ) {
				$option[ $checkbox ] = -1;
			}
		}

		return $option;
	}

	public static function list(): array {
		$saved_settings = get_option( 'slim_seo' ) ?: [];
		$settings       = [
			'enable_edit_robots' => 0,
			'custom_robots'      => '',
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

		$option                       = get_option( 'slim_seo', [] );
		$option['enable_edit_robots'] = 1;
		$option['custom_robots']      = $data;

		return update_option( 'slim_seo', $option );
	}
}
