<?php
namespace SlimSEO;

use SlimSEO\Settings\Settings;

class Code {
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function setup() {
		// Settings.
		add_filter( 'slim_seo_settings_tabs', [ $this, 'add_tab' ] );
		add_filter( 'slim_seo_settings_panes', [ $this, 'add_pane' ] );

		// Output.
		add_action( 'wp_head', [ $this, 'output_header_code' ] );
		add_action( 'wp_body_open', [ $this, 'output_body_code' ] );
		add_action( 'wp_footer', [ $this, 'output_footer_code' ] );
	}

	public function output_header_code() {
		$this->output( 'header_code' );
	}

	public function output_body_code() {
		$this->output( 'body_code' );
	}

	public function output_footer_code() {
		$this->output( 'footer_code' );
	}

	private function output( $key ) {
		if ( is_feed() || is_robots() || is_trackback() ) {
			return;
		}
		$option = get_option( 'slim_seo' ) ?: [];
		$code   = $option[ $key ] ?? '';

		// @codingStandardsIgnoreLine.
		echo $code;
	}

	/**
	 * Insert "Code" tab after "General" tab.
	 */
	public function add_tab( array $tabs ) : array {
		$new = [];
		foreach ( $tabs as $key => $value ) {
			$new[ $key ] = $value;
			if ( $key === 'general' ) {
				$new['code'] = __( 'Code', 'slim-seo' );
			}
		}
		return $new;
	}

	public function add_pane( array $panes ) : array {
		$panes['code'] = $this->settings->get_pane( 'code' );
		return $panes;
	}
}
