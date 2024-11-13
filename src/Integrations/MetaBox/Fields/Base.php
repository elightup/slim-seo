<?php
namespace SlimSEO\Integrations\MetaBox\Fields;

use RWMB_Field;

class Base {
	public static $field;

	public static function parse( $value, $field ) {
		self::$field = $field;
		// Only get the 1st item.
		if ( $field['clone'] ) {
			$value = (array) $value;
			$value = reset( $value );
		}
		return static::get_clone_value( $value );
	}

	public static function get_clone_value( $value ) {
		// Only get the 1st item.
		if ( self::$field['multiple'] ) {
			$value = (array) $value;
			$value = reset( $value );
		}
		return static::get_single_value( $value );
	}

	public static function get_single_value( $value ) {
		return RWMB_Field::call( 'format_single_value', self::$field, $value, [], null );
	}
}
