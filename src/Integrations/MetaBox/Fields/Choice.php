<?php
namespace SlimSEO\Integrations\MetaBox\Fields;

use RWMB_Field;

class Choice extends Base {
	public static function get_single_value( $value ) {
		return RWMB_Field::call( 'format_single_value', self::$field, $value, '', null );
	}
}
