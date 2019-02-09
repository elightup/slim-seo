<?php
namespace SlimSEO\MetaTags;

class Twitter {
	public function __construct() {
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	/**
	 * Twitter uses OpenGraph, so no need to output duplicated tags.
	 *
	 * @link https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/getting-started
	 */
	public function output() {
		echo '<meta name="twitter:card" content="summary_large_image">', "\n";
	}
}
