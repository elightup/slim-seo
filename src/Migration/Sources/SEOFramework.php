<?php
namespace SlimSEO\Migration\Sources;

class SEOFramework extends Source {
	protected $constant = 'THE_SEO_FRAMEWORK_VERSION';

	protected function get_post_title( $post_id ) {
		$post_title = get_post_meta( $post_id, '_genesis_title', true );
		return empty( $post_title ) ? '' : $post_title . ' - ' . get_bloginfo( 'name' );
	}

	protected function get_post_description( $post_id ) {
		return get_post_meta( $post_id, '_genesis_description', true );
	}

	protected function get_post_facebook_image( $post_id ) {
		return get_post_meta( $post_id, '_social_image_url', true );
	}

	protected function get_post_noindex( $post_id ) {
		return (int) get_post_meta( $post_id, '_genesis_noindex', true );
	}

	protected function get_term_title( $term_id ) {
		$term = $this->get_term( $term_id );
		return $term['doctitle'] ?? '';
	}

	protected function get_term_description( $term_id ) {
		$term = $this->get_term( $term_id );
		return $term['description'] ?? '';
	}

	protected function get_term_facebook_image( $term_id ) {
		$term = $this->get_term( $term_id );
		return $term['social_image_url'] ?? '';
	}

	protected function get_term_noindex( $term_id ) {
		$term = $this->get_term( $term_id );
		return intval( ! empty( $term['noindex'] ) );
	}

	private function get_term( $term_id ) {
		return get_term_meta( $term_id, 'autodescription-term-settings', true );
	}
}
