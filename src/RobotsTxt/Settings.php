<?php
namespace SlimSEO\RobotsTxt;

use SlimSEO\Helpers\Assets;

class Settings {
	private $loader;

	public function __construct( Loader $loader ) {
		$this->loader = $loader;

		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_option', [ $this, 'save' ], 10, 2 );
	}

	public function enqueue(): void {
		Assets::enqueue_build_js( 'robots', 'SSRobots', [
			'settings'     => self::list(),
			'fileExists'   => file_exists( ABSPATH . 'robots.txt' ),
			'defaultValue' => $this->loader->load_default_robots_txt_content(),
		] );
	}

	public function save( array $option, array $data ): array {
		if ( empty( $data['robots_txt_editable'] ) ) {
			$option['robots_txt_editable'] = 0;
			$option['robots_txt_content']  = '';
		} else {
			$option['robots_txt_editable'] = 1;
		}

		return $option;
	}

	private static function list(): array {
		$saved_settings = get_option( 'slim_seo' ) ?: [];
		$settings       = [
			'robots_txt_editable' => $saved_settings['robots_txt_editable'] ?? 0,
			'robots_txt_content'  => $saved_settings['robots_txt_content'] ?? '',
		];

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
