<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\Hook;

class WPForo {
	private $hook;

	public function __construct( Hook $hook ) {
		$this->hook = $hook;
	}

	public function is_active(): bool {
		return defined( 'WPFORO_VERSION' );
	}

	public function setup(): void {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process(): void {
		if ( wpforo_setting( 'seo', 'seo_meta' ) && is_wpforo_page() ) {
			$this->hook->remove();
		}
	}
}
