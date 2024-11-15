<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Taxonomy extends Base {
	public function get_value() {
		$value = $this->field['value'];

		if ( empty( $value ) ) {
			return null;
		}

		$term = is_array( $value ) ? reset( $value ) : $value;
		$term = get_term( $term );

		return $term ? $term->name : null;
	}
}
