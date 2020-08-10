<?php
namespace SlimSEO\Integrations;

class Jetpack {
	public function setup() {
		add_filter( 'jetpack_disable_seo_tools', '__return_true' );
		add_filter( 'jetpack_enable_open_graph', '__return_false' );
		add_filter( 'jetpack_seo_meta_tags_enabled', '__return_false' );
	}
}
