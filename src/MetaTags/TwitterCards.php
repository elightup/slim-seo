<?php
namespace SlimSEO\MetaTags;

class TwitterCards {
	private $image_obj;

	public function __construct() {
		$this->image_obj = new Image( 'twitter_image' );
	}

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

		$image = $this->image_obj->get_value() ?: $this->get_default_image();
		$image = $image['src'] ?? '';
		$image = apply_filters( 'slim_seo_twitter_card_image', $image );
		if ( ! empty( $image ) ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">', "\n";
		}

		$site = $this->get_site();
		$site = apply_filters( 'slim_seo_twitter_card_site', $site );
		if ( $site ) {
			echo '<meta name="twitter:site" content="' . esc_attr( $site ) . '">', "\n";
		}
	}

	private function get_default_image() : array {
		$data = get_option( 'slim_seo' );
		return empty( $data['default_twitter_image'] ) ? [] : $this->image_obj->get_data_from_url( $data['default_twitter_image'] );
	}

	private function get_site() : string {
		$data = get_option( 'slim_seo', [] );
		return $data['twitter_site'] ?? '';
	}
}
