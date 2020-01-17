<?php
namespace SlimSEO\Migration;

class Yoast extends Replacer {
	public function get_title( $post_id ) {
		$post         = get_post( $post_id, ARRAY_A );
		$title        = get_post_meta( $post_id, '_yoast_wpseo_title', true );
		$parsed_title = wpseo_replace_vars( $title, $post );
		return $parsed_title;
	}

	public function get_description( $post_id ) {
		$post               = get_post( $post_id, ARRAY_A );
		$description        = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		$parsed_description = wpseo_replace_vars( $description, $post );
		return $parsed_description;
	}

	public function get_facebook_image( $post_id ) {
		return get_post_meta( $post_id, '_yoast_wpseo_opengraph-image', true );
	}

	public function get_twitter_image( $post_id ) {
		return get_post_meta( $post_id, '_yoast_wpseo_twitter-image', true );
	}
}
