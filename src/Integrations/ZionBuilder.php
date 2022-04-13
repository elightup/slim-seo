<?php
namespace SlimSEO\Integrations;

class ZionBuilder {
	public function setup() {
		if ( ! class_exists( '\ZionBuilder\Plugin' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ] );
	}

	public function description( $description ) {
		$content = \ZionBuilder\Plugin::$instance->renderer->get_content( get_the_ID() );

		return $content ?: $description;
	}
}
