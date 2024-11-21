<?php
namespace SlimSEO\Integrations\ACF\Fields;

class File extends Base {
	public function get_value() {
		$value = $this->field['value'];

		return isset( $value['url'] ) ? $value['url'] : ( is_numeric( $value ) ? wp_get_attachment_url( $value ) : $value );
	}
}
