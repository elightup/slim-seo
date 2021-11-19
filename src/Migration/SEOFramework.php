<?php
namespace SlimSEO\Migration;

class SEOFramework extends Replacer {
	public function get_post_title( $post_id ) {
		$post_title = get_post_meta( $post_id, '_genesis_title', true );
		return empty( $post_title ) ? '' : $post_title . ' - ' . get_bloginfo( 'name' );
	}

	public function get_post_description( $post_id ) {
		return get_post_meta( $post_id, '_genesis_description', true );
	}

	public function get_post_facebook_image( $post_id ) {
		return get_post_meta( $post_id, '_social_image_url', true );
	}

	public function get_post_noindex( $post_id ) {
		return intval( get_post_meta( $post_id, '_genesis_noindex', true ) );
	}

	public function get_term_title( $term_id ) {
		$term = $this->get_term( $term_id );
		return empty( $term['doctitle'] ) ? '' : $term['doctitle'];
	}

	public function get_term_description( $term_id ) {
		$term = $this->get_term( $term_id );
		return empty( $term['description'] ) ? '' : $term['description'];
	}

	public function get_term_facebook_image( $term_id ) {
		$term = $this->get_term( $term_id );
		return empty( $term['social_image_url'] ) ? '' : $term['social_image_url'];
	}

	public function get_term_noindex( $term_id ) {
		$term = $this->get_term( $term_id );
		return intval( ! empty( $term['noindex'] ) );
	}

	private function get_term( $term_id ) {
		return get_term_meta( $term_id, 'autodescription-term-settings', true );
	}
}
