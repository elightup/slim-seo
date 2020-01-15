<?php
namespace SlimSEO;

class Code {
	public function setup() {
		add_action( 'wp_head', [ $this, 'output_header_code' ] );
		add_action( 'wp_footer', [ $this, 'output_footer_code' ] );
	}

	public function output_header_code() {
		$this->output_code( 'header_code' );
	}

	public function output_footer_code() {
		$this->output_code( 'footer_code' );
	}

	private function output_code( $key ) {
		if ( is_feed() || is_robots() || is_trackback() ) {
			return;
		}
		$option = get_option( 'slim_seo' );
		$code   = isset( $option[ $key ] ) ? $option[ $key ] : '';
		echo $code;
	}
}
