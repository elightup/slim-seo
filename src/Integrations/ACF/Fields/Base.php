<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Base {
	protected $field;

	public function __construct( array $field, $default_value ) {
		$this->field          = $field;

		// Only get the 1st item.
		$this->field['value'] = is_array( $this->field['value'] ) ? reset( $this->field['value'] ) : ( $this->field['value'] ?: $default_value );
	}

	public function get_value() {
		return wp_strip_all_tags( $this->field['value'], true );
	}
}
