<?php
namespace SlimSEO\Integrations\MetaBox\Fields;

use RWMB_Field;

class Base {
	public static $field;

	public static function parse( $value, $field ) {
		self::$field = $field;
		return $field['clone'] ? array_map( [ static::class, 'get_clone_value' ], (array) $value ) : static::get_clone_value( $value );
	}

	public static function get_clone_value( $clone ) {
		return self::$field['multiple'] ? array_map( [ static::class, 'get_single_value' ], (array) $clone ) : static::get_single_value( $clone );
	}

	public static function get_single_value( $value ) {
		return RWMB_Field::call( 'format_single_value', self::$field, $value, [], null );
	}
}
