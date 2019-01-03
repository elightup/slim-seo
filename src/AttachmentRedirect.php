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

		$parent = get_queried_object()->post_parent;
		$to     = home_url( '/' );

		if ( $parent && 'trash' !== get_post_status( $parent ) ) {
			$to = get_permalink( $parent );
		}

		wp_safe_redirect( esc_url( $to ), 301 );
		die;
	}
}
