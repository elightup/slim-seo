<?php
namespace SlimSEO\Migration;

class ReplacerFactory {
	public static function make( $from_plugin ) {
		switch ( $from_plugin ) {
			case 'yoast': return new Yoast;
			case 'aioseo': return new AIOSEO;
		}
	}
}