<?php
namespace SlimSEO;

class Activator {
	public function __construct( $file ) {
		register_activation_hook( $file, [ $this, 'activate' ] );
		add_action( 'activated_plugin', [ $this, 'redirect' ], 10, 2 );
	}

	public function activate( $network_wide ) {
		if ( is_multisite() && $network_wide ) {
			$this->network_activate();
		} else {
			$this->site_activate();
		}
	}

	private function network_activate() {
		$sites = get_sites( [
			'fields'            => 'ids',
			'number'            => 0,
			'update_site_cache' => false,
		] );
		foreach ( $sites as $site ) {
			switch_to_blog( $site );
			$this->site_activate();
			restore_current_blog();
		}
	}

	private function site_activate() {
		// Update rewrite rules. @see Deactivator class.
		delete_option( 'rewrite_rules' );
	}

	public function redirect( $plugin, $network_wide = false ) {
		$is_cli    = 'cli' === php_sapi_name();
		$is_plugin = 'slim-seo/slim-seo.php' === $plugin;

		$action           = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$checked          = isset( $_POST['checked'] ) && is_array( $_POST['checked'] ) ? count( $_POST['checked'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		$is_bulk_activate = $action === 'activate-selected' && $checked > 1;
		$is_doing_ajax    = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( ! $is_plugin || $network_wide || $is_cli || $is_bulk_activate || $this->is_bundled() || $is_doing_ajax ) {
			return;
		}
		wp_safe_redirect( admin_url( 'options-general.php?page=slim-seo' ) );
		die;
	}

	private function is_bundled(): bool {
		foreach ( $_REQUEST as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( str_contains( $key, 'tgmpa' ) || ( is_string( $value ) && str_contains( $value, 'tgmpa' ) ) ) {
				return true;
			}
		}
		return false;
	}
}
