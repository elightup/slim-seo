<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\SlimSEOHead;

class WPForo {
	private $head;

	public function __construct(
		SlimSEOHead $slim_seo_head
	) {
		$this->head = $slim_seo_head;
	}

	public function is_active(): bool {
		return defined( 'WPFORO_VERSION' );
	}

	public function setup(): void {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process(): void {
		if ( ! wpforo_setting( 'seo', 'seo_meta' ) || ! is_wpforo_page() ) {
			return;
		}

		remove_action( 'wp_head', [ $this->head, 'slim_seo_head' ], 1 );
	}
}
