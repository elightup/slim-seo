<?php
namespace SlimSEO\Helpers;

class Option {
	public static function get( string $name, $default = null ) {
		$option = get_option( 'slim_seo', [] );
		return Arr::get( $option, $name, $default );
	}
}
