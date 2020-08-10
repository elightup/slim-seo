<?php
namespace SlimSEO\Integrations;

class Jetpack {
	public function setup() {
		add_filter( 'jetpack_enable_open_graph', '__return_false' );
	}
}
