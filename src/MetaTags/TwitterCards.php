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

		$default_image = $this->get_default_image();
		$image_obj = new Image( 'twitter_image' );
		$image     = $image_obj->get_value() ?: $default_image;
		if ( ! empty( $image ) ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image[0] ) . '">', "\n";
		}
	}

	private function get_default_image() {
		$data = get_option( 'slim_seo' );
		if ( empty( $data[ 'default_twitter_image' ] ) ) {
			return null;
		}
		$image_id = attachment_url_to_postid( $data[ 'default_twitter_image' ] );
		return $image_id ? wp_get_attachment_image_src( $image_id, 'full' ) : [ $data[ 'default_twitter_image' ] ];
	}
}
