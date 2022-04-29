<?php
namespace SlimSEO\MetaTags;

class Title {
	use Context;

	public function setup() {
		add_theme_support( 'title-tag' );
		add_filter( 'pre_get_document_title', [ $this, 'filter_title' ] );
	}

	public function get_title() {
		return wp_get_document_title();
	}

	public function filter_title( $title ) {
		$custom_title = $this->get_value();

		$title = $custom_title ?: $title;
		$title = apply_filters( 'slim_seo_meta_title', $title, $this );
		$title = Helper::normalize( $title );

		return $title;
	}

	private function get_home_value() {
		$data = get_option( 'slim_seo' );
		return isset( $data['home_title'] ) ? $data['home_title'] : null;
	}

	/**
	 * Make public to allow access from other class.
	 */
	public function get_singular_value( $post_id = null ) {
		$post_id = $post_id ?: get_queried_object_id();
		$data    = get_post_meta( $post_id, 'slim_seo', true );
		return isset( $data['title'] ) ? $data['title'] : null;
	}

	/**
	 * Make public to allow access from other class.
	 */
	public function get_term_value( $term_id = null ) {
		$term_id = $term_id ?: get_queried_object_id();
		$data    = get_term_meta( $term_id, 'slim_seo', true );
		return isset( $data['title'] ) ? $data['title'] : null;
	}
}
