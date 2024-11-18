<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Choice extends Base {
	public function get_value() {
		$value = $this->field['value'];

		if ( isset( $value['label'] ) ) {
			return $value['label'];
		}

		return $value['label'] ?? $value;
	}
}
