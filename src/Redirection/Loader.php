<?php
namespace SlimSEO\Redirection;

class Loader {
	public function init() {
		if ( is_admin() ) {
			new Settings;
		} else {
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
			} else {
				add_action( 'init', [ 'SlimSEO\Redirection\Redirects', 'handle' ] , 1 );
				add_action( 'template_redirect', [ 'SlimSEO\Redirection\Redirection404', 'handle' ], 1 );
			}
		}

		new Api\Redirects;
		new Api\Log404;
	}
}