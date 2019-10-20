<?php
namespace SlimSEO\MetaTags;

class Title {
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'add_title_tag_support' ] );
		add_filter( 'pre_get_document_title', [ $this, 'filter_title' ] );
		add_filter( 'pre_get_document_title', [ __NAMESPACE__ . '\Helper', 'normalize' ], 99 );
	}

	public function add_title_tag_support() {
		add_theme_support( 'title-tag' );
	}

	public function get_title() {
		return wp_get_document_title();
	}

	public function filter_title( $title ) {
		$custom_title = '';

		if ( is_singular() ) {
			$custom_title = $this->get_singular_title();
		}
		if ( is_category() || is_tag() || is_tax() ) {
			$custom_title = $this->get_term_title();
		}

		$title = $custom_title ?: $title;
		$title = apply_filters( 'slim_seo_meta_title', $title );

		return $title;
	}

	private function get_singular_title() {
		$data = get_post_meta( get_the_ID(), 'slim_seo', true );
		return ! empty( $data['title'] ) ? $data['title'] : null;
	}

	private function get_term_title() {
		$data = get_term_meta( get_queried_object_id(), 'slim_seo', true );
		return ! empty( $data['title'] ) ? $data['title'] : null;
	}
}
