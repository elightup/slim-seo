<?php
namespace SlimSEO\Migration;

class ReplacerFactory {
	public static function make( $platform ) {
		switch ( $platform ) {
			case 'yoast': return new Yoast;
			case 'aioseo': return new AIOSEO;
			case 'seo-framework': return new SEOFramework;
			case 'rank-math': return new RankMath;
		}
	}
}
