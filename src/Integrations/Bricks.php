<?php
namespace SlimSEO\Integrations;

class Bricks {
	public function setup() {
		if ( ! defined( 'BRICKS_VERSION' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );

		add_filter( 'bricks/frontend/disable_opengraph', '__return_true' );
		add_filter( 'bricks/frontend/disable_seo', '__return_true' );
	}

	public function description( $description, $post ) {
		$data = \Bricks\Helpers::get_bricks_data( $post->ID );
		if ( $data ) {
			$description = \Bricks\Frontend::render_data( $data, $post->ID, 'content', true );
		}

		return $description;
	}
}
