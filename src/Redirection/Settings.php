<?php
namespace SlimSEO\Redirection;

use SlimSEO\Redirection\Database\Log404 as DbLog;

class Settings {
	protected $db_log;

	public function __construct( DbLog $db_log ) {
		$this->db_log = $db_log;

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
		wp_enqueue_style( 'slim-seo-react-tabs', SLIM_SEO_URL . 'css/react-tabs.css', [], filemtime( SLIM_SEO_DIR . '/css/react-tabs.css' ) );
		wp_enqueue_style( 'slim-seo-redirection', SLIM_SEO_URL . 'css/redirection.css', [ 'wp-components' ], filemtime( SLIM_SEO_DIR . 'css/redirection.css' ) );

		wp_enqueue_script( 'slim-seo-redirection', SLIM_SEO_URL . 'js/redirection.js', [ 'wp-element', 'wp-components', 'wp-i18n' ], filemtime( SLIM_SEO_DIR . 'js/redirection.js' ), true );

		wp_set_script_translations( 'slim-seo-redirection', 'slim-seo', SLIM_SEO_DIR . 'languages/' );

		$this->db_log->create_table();

		$localized_data = [
			'rest'               => untrailingslashit( rest_url() ),
			'nonce'              => wp_create_nonce( 'wp_rest' ),
			'homeURL'            => untrailingslashit( home_url() ),
			'settingsName'       => 'slim_seo',
			'settings'           => self::list(),
			'redirectTypes'      => Helper::redirect_types(),
			'conditionOptions'   => Helper::condition_options(),
			'isLog404TableExist' => $this->db_log->table_exists(),
			'permalinkUrl'       => admin_url( 'options-permalink.php' ),
			'defaultRedirect'    => [
				'id'               => 0,
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
			'auto_redirection',
			'enable_404_logs',
		];

		foreach ( $checkboxes as $checkbox ) {
			if ( empty( $data[ $checkbox ] ) ) {
				$option[ $checkbox ] = -1;
			}
		}

		if ( ! empty( $data['delete_404_log_table'] ) ) {
			$this->db_log->drop_table();
			unset( $option['delete_404_log_table'] );
		}

		return $option;
	}

	public static function list() : array {
		$saved_settings = get_option( 'slim_seo' ) ?: [];
		$settings       = [
			'force_trailing_slash' => 0,
			'auto_redirection'     => 1,
			'redirect_www'         => '',
			'enable_404_logs'      => 0,
			'auto_delete_404_logs' => 30,
			'redirect_404_to'      => '',
			'redirect_404_to_url'  => '',
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
}
