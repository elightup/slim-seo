<?php
namespace SlimSEO\Migration;

class ReplacerFactory {
	public static function make( $platform ) {
		switch ( $platform ) {
			case 'yoast':
				return new Platforms\Yoast;
			case 'aioseo':
				return new Platforms\AIOSEO;
			case 'seo-framework':
				return new Platforms\SEOFramework;
			case 'rank-math':
				return new Platforms\RankMath;
			case 'seopress':
				return new Platforms\SEOPress;
			case 'redirection':
				return new Platforms\Redirection;
			case '301-redirects':
				return new Platforms\Redirects301;
		}
	}
}
