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

	public function get_term_title( $term_id, $term ) {
		$title = $term['wpseo_title'] ?? '';
		$parsed_title = wpseo_replace_vars( $title, $term );
		return $parsed_title;
	}

	public function get_term_description( $term_id, $term ) {
		$description = $term['wpseo_desc'] ?? '';
		$parsed_description = wpseo_replace_vars( $description, $term );
		return $parsed_description;
	}

	public function get_term_facebook_image( $term_id, $term ) {
		return $term['wpseo_opengraph-image'] ?? '';
	}

	public function get_term_twitter_image( $term_id, $term ) {
		return $term['wpseo_twitter-image'] ?? '';
	}

	public function get_terms( $threshold ) {
		$terms = get_option( 'wpseo_taxonomy_meta' );
		if ( empty( $terms ) ) {
			return '';
		}

		$offset                = isset( $_SESSION['processed'] ) ? $_SESSION['processed'] : 0;
		$_SESSION['processed'] = $_SESSION['processed'] + $threshold;

		$terms_array = [];
		$terms = array_values( $terms );
		foreach( $terms as $term ) {
			$terms_array = $terms_array + $term;
		}
		$extract = array_slice( $terms_array, $offset, $threshold, true );
		return $extract;
	}

	public function is_plugin_activation() {
		return defined( 'WPSEO_VERSION' );
	}
}
