<?php
namespace SlimSEO\MetaTags;

class TwitterCards {
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

		$image_url = $this->get_image_url();
		if ( $image_url ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image_url ) . '">', "\n";
		}
	}

	private function get_image_url() {
		if ( ! is_singular() && ! is_tax() && ! is_category() && ! is_tag() ) {
			return null;
		}
		$type = is_singular() ? 'post' : 'term';
		$data = get_metadata( $type, get_queried_object_id(), 'slim_seo', true );

		return empty( $data['twitter_image'] ) ? null : $data['twitter_image'];
	}
}
