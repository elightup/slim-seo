<?php
namespace SlimSEO\MetaTags;

class Hook {
	public function setup(): void {
		add_action( 'wp_head', [ $this, 'output' ], 1 );
	}

	public function remove(): void {
		remove_action( 'wp_head', [ $this, 'output' ], 1 );
	}

	public function output(): void {
		do_action( 'slim_seo_head' );
	}
}