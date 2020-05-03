<?php
namespace SlimSEO\MetaTags;

class TwitterCards {
	use Context;

	public function setup() {
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	/**
	 * Twitter uses OpenGraph, so no need to output duplicated tags.
	 *
	 * @link https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/getting-started
	 */
	public function output() {
		echo '<meta name="twitter:card" content="summary_large_image">', "\n";

		$image_obj = new Image( 'twitter_image' );
		$image     = $image_obj->get_value();
		if ( ! empty( $image ) ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image[0] ) . '">', "\n";
		}
	}
}
