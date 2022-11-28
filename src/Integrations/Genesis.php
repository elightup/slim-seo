<?php
namespace SlimSEO\Integrations;

class Genesis {
	public function __construct() {
		add_filter( 'genesis_detect_seo_plugins', [ $this, 'add_slim_seo' ] );
	}

	public function setup() {
		add_filter( 'genesis_disable_microdata', '__return_true' );
		remove_filter( 'document_title_parts', 'genesis_document_title_parts' );
		remove_filter( 'document_title_separator', 'genesis_document_title_separator' );
	}

	public function add_slim_seo( $data ) {
		$data['constants'][] = 'SLIM_SEO_VER';
		return $data;
	}
}
