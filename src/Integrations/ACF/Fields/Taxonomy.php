<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Taxonomy extends Base {
	public function get_value() {
		$value = $this->field['value'];

		if ( empty( $value ) ) {
			return null;
		}

		$term = get_term( $value );

		return $term ? $term->name : null;
	}
}
