<?php
namespace SlimSEO\Integrations;

class WPForo {
	public function is_active(): bool {
		return defined( 'WPFORO_VERSION' );
	}

	public function setup(): void {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process(): void {
		if ( ! wpforo_setting( 'seo', 'seo_meta' ) || ! is_wpforo_page() ) {
			return;
		}

		// Remove all Slim SEO hooks to 'wp_head' to output meta tags.
		global $wp_filter;
		foreach ( $wp_filter['wp_head'][10] as $callback ) {
			if ( ! is_array( $callback['function'] ) ) {
				continue;
			}
			$instance = $callback['function'][0];
			if ( ! is_object( $instance ) || ! str_contains( get_class( $instance ), 'SlimSEO' ) ) {
				continue;
			}

			remove_filter( 'wp_head', $callback['function'] );
		}
	}
}
