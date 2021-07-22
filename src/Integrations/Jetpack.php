<?php
namespace SlimSEO\Integrations;

class Jetpack {
	public function setup() {
		add_filter( 'jetpack_disable_seo_tools', '__return_true' );
		add_filter( 'jetpack_enable_open_graph', '__return_false' );
		add_filter( 'jetpack_seo_meta_tags_enabled', '__return_false' );

		// Jetpack Boost
		add_filter( 'jetpack_boost_should_defer_js', [ $this, 'disable_defer_js_for_sitemap' ] );
	}

	public function disable_defer_js_for_sitemap( $value ) {
		return empty( get_query_var( 'ss_sitemap' ) ) ? $value : false;
	}
}
