<?php
namespace SlimSEO\Integrations;

class Jetpack {
	public function setup() {
		add_filter( 'jetpack_disable_seo_tools', '__return_true' );
		add_filter( 'jetpack_enable_open_graph', '__return_false' );
		add_filter( 'jetpack_seo_meta_tags_enabled', '__return_false' );

		// Jetpack Boost
		add_filter( 'jetpack_boost_should_defer_js', [ $this, 'disable_defer_js_for_sitemap' ] );

		add_action( 'slim_seo_sitemap_before_output', [ $this, 'disable_photon_for_sitemap' ] );
	}

	public function disable_defer_js_for_sitemap( $value ) {
		return empty( get_query_var( 'ss_sitemap' ) ) ? $value : false;
	}

	public function disable_photon_for_sitemap() {
		add_filter( 'jetpack_photon_skip_image', '__return_true' );
	}
}
