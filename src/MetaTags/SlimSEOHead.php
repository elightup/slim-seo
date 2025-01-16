<?php
namespace SlimSEO\MetaTags;

defined( 'ABSPATH' ) || die;

class SlimSEOHead {

	public function setup() {
		add_action( 'wp_head', [ $this, 'slim_seo_head' ], 1 );
	}

	public function slim_seo_head() {
		do_action( 'slim_seo_head' );
	}
}