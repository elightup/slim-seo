<?php
namespace SlimSEO\Migration;

class Factory {
	public static function make( string $source ): ?Sources\Source {
		switch ( $source ) {
			case 'yoast':
				return new Sources\Yoast;
			case 'aioseo':
				return new Sources\AIOSEO;
			case 'seo-framework':
				return new Sources\SEOFramework;
			case 'rank-math':
				return new Sources\RankMath;
			case 'seopress':
				return new Sources\SEOPress;
			case 'redirection':
				return new Sources\Redirection;
			case '301-redirects':
				return new Sources\Redirects301;
		}

		return null;
	}
}
