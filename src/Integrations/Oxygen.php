<?php
namespace SlimSEO\Integrations;

class Oxygen {
	public function setup() {
		if ( ! defined( 'CT_VERSION' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ] );
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
		add_filter( 'slim_seo_meta_box_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_sitemap_post_types', [ $this, 'remove_post_types' ] );
	}

	public function description( $description ) {
		// In builder mode.
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			return $description;
		}

		$shortcode = get_post_meta( get_the_ID(), 'ct_builder_shortcodes', true );
		return $shortcode ?: $description;
	}

	public function skip_shortcodes( $shortcodes ) {
		$shortcodes[] = 'ct_slider';
		$shortcodes[] = 'ct_code_block';
		return $shortcodes;
	}

	public function remove_post_types( $post_types ) {
		unset( $post_types['ct_template'] );
		return $post_types;
	}
}
