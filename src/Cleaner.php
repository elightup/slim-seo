<?php
namespace SlimSEO;

class Cleaner {
	public function setup() {
		add_action( 'template_redirect', [ $this, 'clean_header' ] );
	}

	public function clean_header() {
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	}
}
