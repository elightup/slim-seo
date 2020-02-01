<?php
namespace SlimSEO\Migration;

class Yoast extends Replacer {

	public function get_post_title( $post_id ) {
		$post         = get_post( $post_id, ARRAY_A );
		$title        = get_post_meta( $post_id, '_yoast_wpseo_title', true );
		$parsed_title = wpseo_replace_vars( $title, $post );
		return $parsed_title;
	}

	public function get_post_description( $post_id ) {
		$post               = get_post( $post_id, ARRAY_A );
		$description        = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		$parsed_description = wpseo_replace_vars( $description, $post );
		return $parsed_description;
	}

	public function get_post_facebook_image( $post_id ) {
		return get_post_meta( $post_id, '_yoast_wpseo_opengraph-image', true );
	}

	public function get_post_twitter_image( $post_id ) {
		return get_post_meta( $post_id, '_yoast_wpseo_twitter-image', true );
	}

	public function get_term_title( $term_id ) {

	}

	public function get_term_description( $term_id ) {

	}

	public function get_term_facebook_image( $term_id ) {
	}

	public function get_term_twitter_image( $term_id ) {
	}

	public function get_terms() {
		$temrs = get_options( 'wpseo_taxonomy_meta' );
		if ( ! $terms ) {
			return '';
		}
		return $terms;
	}
}
