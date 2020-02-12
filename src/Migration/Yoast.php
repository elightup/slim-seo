<?php
namespace SlimSEO\Migration;

class Yoast extends Replacer {

	public function get_post_title( $post_id ) {
		$post  = get_post( $post_id, ARRAY_A );
		$title = get_post_meta( $post_id, '_yoast_wpseo_title', true );
		return wpseo_replace_vars( $title, $post );
	}

	public function get_post_description( $post_id ) {
		$post        = get_post( $post_id, ARRAY_A );
		$description = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		return wpseo_replace_vars( $description, $post );
	}

	public function get_post_facebook_image( $post_id ) {
		return get_post_meta( $post_id, '_yoast_wpseo_opengraph-image', true );
	}

	public function get_post_twitter_image( $post_id ) {
		return get_post_meta( $post_id, '_yoast_wpseo_twitter-image', true );
	}

	public function get_term_title( $term_id ) {
		$term = $this->get_term( $term_id );
		if ( ! $term ) {
			return '';
		}
		$title = empty( $term['wpseo_title'] ) ? '' : $term['wpseo_title'];
		return wpseo_replace_vars( $title, $term );
	}

	public function get_term_description( $term_id ) {
		$term = $this->get_term( $term_id );
		if ( ! $term ) {
			return '';
		}
		$description = empty( $term['wpseo_desc'] ) ? '' : $term['wpseo_desc'];
		return wpseo_replace_vars( $description, $term );
	}

	public function get_term_facebook_image( $term_id ) {
		$term = $this->get_term( $term_id );
		return empty( $term['wpseo_opengraph-image'] ) ? '' : $term['wpseo_opengraph-image'];
	}

	public function get_term_twitter_image( $term_id ) {
		$term = $this->get_term( $term_id );
		return empty( $term['wpseo_twitter-image'] ) ? '' : $term['wpseo_twitter-image'];
	}

	public function cleanup_posts() {
		global $wpdb;
		$keys = [
			'_yoast_wpseo_title',
			'_yoast_wpseo_metadesc',
			'_yoast_wpseo_opengraph-image',
			'_yoast_wpseo_opengraph-image-id',
			'_yoast_wpseo_twitter-image',
			'_yoast_wpseo_twitter-image-id',
		];
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ('" . implode( "','", $keys ) . "')" );
	}

	public function cleanup_terms() {
		delete_option( 'wpseo_taxonomy_meta' );
	}

	/**
	 * Get terms value from option table.
	 */
	public function get_terms() {
		$terms = get_option( 'wpseo_taxonomy_meta' );
		if ( empty( $terms ) ) {
			return [];
		}
		$terms = array_values( $terms );
		$terms_array = [];
		foreach( $terms as $term ) {
			$terms_array = $terms_array + $term;
		}
		return $terms_array;
	}

	public function get_term( $term_id ) {
		$terms = $this->get_terms();
		return isset( $terms[ $term_id ] ) ? $terms[ $term_id ] : null;
	}

	public function is_activated() {
		return defined( 'WPSEO_VERSION' );
	}
}
