<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Base {
	protected $field;

	public function __construct( array $field, $default_value ) {
		$this->field          = $field;
		$this->field['value'] = $this->field['value'] ?: $default_value;
	}

	public function get_value() {
		return is_array( $this->field['value'] ) ? array_map( 'wp_strip_all_tags', $this->field['value'] ) : wp_strip_all_tags( $this->field['value'], true );
	}
}
