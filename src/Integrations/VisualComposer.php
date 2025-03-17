<?php
namespace SlimSEO\Integrations;

class VisualComposer {
	public function is_active(): bool {
		// WPBakery Page Builder & TagDiv Composer plugins.
		return defined( 'WPB_VC_VERSION' ) || defined( 'TD_COMPOSER' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_allowed_shortcodes', [ $this, 'exclude_shortcodes' ] );
	}

	public function exclude_shortcodes( array $shortcodes ): array {
		return array_filter( $shortcodes, function ( $shortcode ): bool {
			return ! str_starts_with( $shortcode, 'vc_' )
				&& ! str_starts_with( $shortcode, 'td' );
		}, ARRAY_FILTER_USE_KEY );
	}
}
