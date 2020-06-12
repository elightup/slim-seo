<?php
namespace SlimSEO\Integrations;

class Oxygen {
	public function setup() {
		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ] );
	}

	public function description( $description ) {
		// Oxygen not activated.
		if ( ! defined( 'CT_VERSION' ) ) {
			return $description;
		}
		// In builder mode.
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			return $description;
		}

		$shortcode = get_post_meta( get_the_ID(), 'ct_builder_shortcodes', true );
		return $shortcode ? do_shortcode( $shortcode ) : $description;
	}
}