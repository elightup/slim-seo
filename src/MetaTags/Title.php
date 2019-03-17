<?php
namespace SlimSEO\MetaTags;

class Title {
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'add_title_tag_support' ] );
		add_filter( 'pre_get_document_title', [ __NAMESPACE__ . '\Helper', 'normalize' ], 99 );
	}

	public function add_title_tag_support() {
		add_theme_support( 'title-tag' );
	}

	public function get_title() {
		return wp_get_document_title();
	}
}
