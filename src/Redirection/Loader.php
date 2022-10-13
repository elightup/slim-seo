<?php
namespace SlimSEO\Redirection;

class Loader {
	public function __construct() {
		if ( is_admin() ) {
			new Settings;
		} else {
			new Redirection;
			new Redirection404;
		}

		new Api\Redirects;
		new Api\Log404;
	}
}
