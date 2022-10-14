<?php
namespace SlimSEO\Redirection;

class Loader {
	public function __construct() {
		$db_redirects = new Database\Redirects;
		$db_log       = new Database\Log404;

		if ( is_admin() ) {
			new Settings;
		} else {
			new Redirection( $db_redirects );
			new Redirection404( $db_log );
		}

		new Api\Redirects( $db_redirects );
		new Api\Log404( $db_log );
	}
}
