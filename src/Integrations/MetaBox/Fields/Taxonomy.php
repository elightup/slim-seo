<?php
namespace SlimSEO\Integrations\MetaBox\Fields;

class Taxonomy extends Base {
	public static function get_single_value( $value ) {
		return $value ? $value->name : null;
	}
}
