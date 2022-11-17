<?php
namespace SlimSEO\Redirection\Migration;

use SlimSEO\Redirection\Database\Redirects as DbRedirects;

class ReplacerFactory {
	public static function make( string $platform, DbRedirects $db_redirects ) {
		switch ( $platform ) {
			case 'redirection':
				return new Redirection( $db_redirects );
			case '301-redirects':
				return new Redirects301( $db_redirects );
			case 'yoast-premium':
				return new Yoast( $db_redirects );
		}
	}
}
