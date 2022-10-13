<?php
namespace SlimSEO\Redirection;

class Settings {
	public function __construct() {
		add_filter( 'slim_seo_settings_tabs', [ $this, 'add_tab' ] );
		add_filter( 'slim_seo_settings_panes', [ $this, 'add_pane' ] );
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_option', [ $this, 'option_saved' ], 10, 2 );
	}

	public function add_tab( array $tabs ) : array {
		$tabs['redirection'] = __( 'Redirection', 'slim-seo' );
		return $tabs;
	}

	public function add_pane( array $panes ) : array {
		$panes['redirection'] = '<div id="redirection" class="ss-tab-pane"><div id="ss-redirection"></div></div>';

		return $panes;
	}

	public function enqueue() {
		wp_enqueue_style( 'slim-seo-redirection', SLIM_SEO_URL . 'css/redirection.css', [ 'wp-components' ], filemtime( SLIM_SEO_DIR . '/css/redirection.css' ) );

		wp_enqueue_script( 'slim-seo-redirection', SLIM_SEO_URL . 'js/redirection.js', [ 'wp-element', 'wp-components', 'wp-i18n' ], filemtime( SLIM_SEO_DIR . '/js/redirection.js' ), true );

		$localized_data = [
			'rest'             => untrailingslashit( rest_url() ),
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'settingsPageURL'  => untrailingslashit( admin_url( 'options-general.php?page=slim-seo' ) ),
			'tabID'            => 'redirection',
			'homeURL'          => untrailingslashit( home_url() ),
			'settingsName'     => 'slim_seo',
			'settings'         => self::list(),
			'redirectTypes'    => Helper::redirect_types(),
			'conditionOptions' => Helper::condition_options(),
			'defaultRedirect'  => [
				'id'               => -1,
				'type'             => 301,
				'condition'        => 'exact-match',
				'from'             => '',
				'to'               => '',
				'note'             => '',
				'enable'           => 1,
				'ignoreParameters' => 0,
			],
		];

		wp_localize_script( 'slim-seo-redirection', 'SSRedirection', $localized_data );

		do_action( 'slim_seo_redirection_enqueue' );
		do_action( 'slim_seo_redirection_enqueue_settings' );
	}

	public function option_saved( array $option, array $data ) : array {
		$checkboxes = [
			'force_trailing_slash',
			'enable_404_logs',
		];

		foreach ( $checkboxes as $checkbox ) {
			if ( empty( $data[ $checkbox ] ) ) {
				$option[ $checkbox ] = 0;
			}
		}

		return $option;
	}

	public static function list() : array {
		return array_merge(
			[
				'force_trailing_slash' => 0,
				'redirect_www'         => '',
				'enable_404_logs'      => 0,
				'redirect_404_to'      => '',
				'redirect_404_to_url'  => '',
			],
			get_option( 'slim_seo' ) ?: []
		);
	}

	public static function get( string $name ) {
		$settings = self::list();

		return $settings[ $name ] ?? false;
	}
}
