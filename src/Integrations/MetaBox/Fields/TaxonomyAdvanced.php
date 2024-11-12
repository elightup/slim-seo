<?php
namespace SlimSEO\Integrations\MetaBox\Fields;

class TaxonomyAdvanced extends Base {
	public static function get_single_value( $value = null ) {
		// Groups send ID, normal fields send term object.
		$value = get_term( $value );
		if ( ! $value || is_wp_error( $value ) ) {
			return null;
		}
		return $value->name;
	}
}
