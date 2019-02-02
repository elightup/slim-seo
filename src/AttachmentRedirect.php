<?php
namespace SlimSEO;

class AttachmentRedirect {
	public function __construct() {
		add_action( 'template_redirect', [ $this, 'redirect_attachment' ] );
	}

	public function redirect_attachment() {
		if ( ! is_attachment() ) {
			return;
		}

		$to = wp_get_attachment_url( get_queried_object_id() );
		wp_safe_redirect( esc_url( $to ), 301 );
		die;
	}
}
