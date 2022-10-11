<?php
namespace SlimSEO\Redirection;

class Loader {
	public function __construct() {
		if ( is_admin() ) {
			new Settings;
		} else {
			add_action( 'plugins_loaded', [ __NAMESPACE__ . '\Redirects', 'handle' ], 1 );
			add_action( 'template_redirect', [ __NAMESPACE__ . '\Redirection404', 'handle' ], 1 );
			add_filter( 'user_trailingslashit', [ __NAMESPACE__ . '\Redirects', 'force_trailing_slash' ], 1000, 2 );
			add_action( 'plugins_loaded', [ __NAMESPACE__ . '\Redirects', 'redirect_www' ], 2 );
		}

		new Api\Redirects;
		new Api\Log404;
	}
}
