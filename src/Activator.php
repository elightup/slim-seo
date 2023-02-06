<?php
namespace SlimSEO;

class Activator {
	public function __construct( $file ) {
		register_activation_hook( $file, [ $this, 'activate' ] );

		add_filter( 'plugin_action_links_slim-seo/slim-seo.php', [ $this, 'add_plugin_action_links' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_plugin_meta_links' ], 10, 2 );

		add_action( 'activated_plugin', [ $this, 'redirect' ], 10, 2 );
	}

	public function activate( $network_wide ) {
		if ( is_multisite() && $network_wide ) {
			$this->network_activate();
		} else {
			$this->site_activate();
		}
	}

	public function add_plugin_action_links( array $links ) : array {
		$links[]     = '<a href="' . esc_url( admin_url( 'options-general.php?page=slim-seo' ) ) . '">' . __( 'Settings', 'slim-seo' ) . '</a>';
		$upgradeable = apply_filters( 'slim_seo_upgradeable', true );
		if ( $upgradeable ) {
			$links[] = '<a href="https://wpslimseo.com/products/?utm_source=WordPress&utm_medium=link&utm_campaign=plugin" style="color: #39b54a; font-weight: bold">' . esc_html__( 'Upgrade', 'slim-seo' ) . '</a>';
		}
		return $links;
	}

	public function add_plugin_meta_links( array $meta, string $file ) {
		if ( $file !== 'slim-seo/slim-seo.php' ) {
			return $meta;
		}

		$meta[] = '<a href="https://docs.wpslimseo.com" target="_blank">' . esc_html__( 'Documentation', 'slim-seo' ) . '</a>';
		$meta[] = '<a href="https://wordpress.org/support/plugin/slim-seo/reviews/?filter=5" target="_blank" title="' . esc_html__( 'Rate Slim SEO on WordPress.org', 'slim-seo' ) . '" style="color: #ffb900">'
			. str_repeat( '<span class="dashicons dashicons-star-filled" style="font-size: 16px; width:16px; height: 16px"></span>', 5 )
			. '</a>';

		return $meta;
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

		if ( ! $is_plugin || $network_wide || $is_cli || $is_bulk_activate || $this->is_bundled() ) {
			return;
		}
		wp_safe_redirect( admin_url( 'options-general.php?page=slim-seo' ) );
		die;
	}

	private function is_bundled() : bool {
		foreach ( $_REQUEST as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( false !== strpos( $key, 'tgmpa' ) || ( ! is_array( $value ) && false !== strpos( $value, 'tgmpa' ) ) ) {
				return true;
			}
		}
		return false;
	}
}
