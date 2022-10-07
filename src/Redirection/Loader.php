<?php
namespace SlimSEO\Redirection;

class Loader {
	public function __construct() {
		if ( is_admin() ) {
			new Settings;
		} else {
			add_action( 'init', [ __NAMESPACE__ . '\Redirects', 'handle' ], 1 );
			add_action( 'template_redirect', [ __NAMESPACE__ . '\Redirection404', 'handle' ], 1 );
		}

		new Api\Redirects;
		new Api\Log404;
	}
}
