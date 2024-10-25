<?php
namespace SlimSEO\Helpers;

class Option {
	public static function get( string $name, $default_value = null ) {
		$option = get_option( 'slim_seo', [] );
		return Arr::get( $option, $name, $default_value );
	}
}
