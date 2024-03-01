<?php
namespace SlimSEO;

class Core {
	public function setup() {
		$this->load_textdomain();

		add_filter( 'plugin_action_links_slim-seo/slim-seo.php', [ $this, 'add_plugin_action_links' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_plugin_meta_links' ], 10, 2 );
	}

	private function load_textdomain() {
		unload_textdomain( 'slim-seo' );
		load_plugin_textdomain( 'slim-seo', false, basename( dirname( __DIR__ ) ) . '/languages/' );
	}

	public function add_plugin_action_links( array $links ): array {
		$links[]     = '<a href="' . esc_url( admin_url( 'options-general.php?page=slim-seo' ) ) . '">' . __( 'Settings', 'slim-seo' ) . '</a>';
		$upgradeable = apply_filters( 'slim_seo_upgradeable', true );
		if ( $upgradeable ) {
			$links[] = '<a href="https://elu.to/spp" style="color: #39b54a; font-weight: bold">' . esc_html__( 'Upgrade', 'slim-seo' ) . '</a>';
		}
		return $links;
	}

	public function add_plugin_meta_links( array $meta, string $file ) {
		if ( $file !== 'slim-seo/slim-seo.php' ) {
			return $meta;
		}

		$meta[] = '<a href="https://docs.wpslimseo.com?utm_source=plugin_links&utm_medium=link&utm_campaign=slim_seo" target="_blank">' . esc_html__( 'Documentation', 'slim-seo' ) . '</a>';
		$meta[] = '<a href="https://wordpress.org/support/plugin/slim-seo/reviews/?filter=5" target="_blank" title="' . esc_html__( 'Rate Slim SEO on WordPress.org', 'slim-seo' ) . '" style="color: #ffb900">'
			. str_repeat( '<span class="dashicons dashicons-star-filled" style="font-size: 16px; width:16px; height: 16px"></span>', 5 )
			. '</a>';

		return $meta;
	}
}
