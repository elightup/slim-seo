<?php
namespace SlimSEO\Integrations;

class LifterLMS {
	public function is_active(): bool {
		return class_exists( 'LifterLMS' );
	}

	public function setup(): void {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process(): void {
		if ( $this->is_skipped_page() ) {
			add_filter( 'slim_seo_post_content', '__return_empty_string' );
		}
	}

	private function is_skipped_page(): bool {
		$pages = [ 'checkout', 'myaccount' ];
		$pages = array_map( 'llms_get_page_id', $pages );
		return is_page( $pages );
	}
}
