<?php
namespace SlimSEO\Integrations;

class AutoListings {
	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! defined( 'AUTO_LISTINGS_VERSION' ) ) {
			return;
		}

		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
	}

	public function skip_shortcodes( array $shortcodes ) : array {
		$shortcodes = array_merge( $shortcodes, [
			'auto_listings_search',
			'auto_listings_listing',
			'auto_listings_listings',
			'auto_listings_contact_form',
			'als_button',
			'als_total_listings',
			'als_selected',
			'als_toggle_wrapper',
			'als_keyword',
			'als_field',
			'als',
		] );
		return $shortcodes;
	}
}
