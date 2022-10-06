<?php
namespace SlimSEO\Redirection;

class Settings {
	public function __construct() {
		add_filter( 'slim_seo_settings_tabs', [ $this, 'add_tab' ] );
		add_filter( 'slim_seo_settings_panes', [ $this, 'add_pane' ] );
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ] );

		add_action( 'slim_seo_save', [ $this, 'save' ] );
	}
	
	public function add_tab( array $tabs ) : array {
		$tabs['redirection'] = __( 'Redirection', 'slim-seo-redirection' );
		return $tabs;
	}

	public function add_pane( array $panes ) : array {
		$panes['redirection'] = '<div id="redirection" class="ss-tab-pane"><div id="ss-redirection"></div></div>';	
		
		return $panes;
	}

	public function enqueue() {
		wp_enqueue_style( 'slim-seo-redirection', SLIM_SEO_URL . 'css/redirection.css', [], SLIM_SEO_VER );

		wp_enqueue_script( 'slim-seo-redirection', SLIM_SEO_URL . 'js/redirection.js', [ 'wp-element', 'wp-i18n', ], SLIM_SEO_VER, true );

		$localized_data = [
			'rest'             => untrailingslashit( rest_url() ),
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'settingsPageURL'  => untrailingslashit( admin_url( 'options-general.php?page = slim-seo' ) ),
			'tabID'            => 'redirection',
			'settingsName'     => SLIM_SEO_REDIRECTION_SETTINGS_OPTION_NAME,
			'settings'         => Helper::get_settings(),
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

	public function save() {
		update_option( SLIM_SEO_REDIRECTION_SETTINGS_OPTION_NAME, $_POST[SLIM_SEO_REDIRECTION_SETTINGS_OPTION_NAME] );
	}
}