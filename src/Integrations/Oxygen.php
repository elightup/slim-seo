<?php
namespace SlimSEO\Integrations;

class Oxygen {
	public function setup() {
		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ] );
	}

	public function description( $description ) {
		$shortcode = get_post_meta( get_the_ID(), 'ct_builder_shortcodes', true );
		return defined( 'CT_VERSION' ) && $shortcode ? do_shortcode( $shortcode ) : $description;
	}
}