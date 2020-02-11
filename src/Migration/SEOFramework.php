<?php
namespace SlimSEO\Migration;

class SEOFramework extends Replacer {

	public function get_post_title( $post_id ) {
		return get_post_meta( $post_id, '_genesis_title', true );
	}

	public function get_post_description( $post_id ) {
		return get_post_meta( $post_id, '_genesis_description', true );
	}

	public function get_post_facebook_image( $post_id ) {
		return get_post_meta( $post_id, '_social_image_url', true );
	}

	public function get_post_twitter_image( $post_id ) {
		return get_post_meta( $post_id, '_social_image_url', true );
	}

	public function get_term_title( $term_id ) {
		$term = $this->get_term( $term_id );
		return $term ? $term['doctitle'] : '';
	}

	public function get_term_description( $term_id ) {
		$term = $this->get_term( $term_id );
		return $term ? $term['description'] : '';
	}

	public function get_term_facebook_image( $term_id ) {
		$term = $this->get_term( $term_id );
		return $term ? $term['social_image_url'] : '';
	}

	public function get_term_twitter_image( $term_id ) {
		$term = $this->get_term( $term_id );
		return $term ? $term['social_image_url'] : '';
	}

	private function get_term( $term_id ) {
		return get_term_meta( $term_id, 'autodescription-term-settings', true );
	}

	public function cleanup_posts() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ('_genesis_title', '_genesis_description', '_social_image_url')" );
	}

	public function cleanup_terms() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE meta_key = 'autodescription-term-settings'" );
	}
}
