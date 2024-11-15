<?php
namespace SlimSEO\Integrations\ACF\Fields;

class User extends Base {
	public function get_value() {
		$value = $this->field['value'];

		if ( ! $value ) {
			return null;
		}

		// Select multiple values.
		if ( is_array( $value ) && empty( $value['display_name'] ) ) {
			$value = reset( $value );
		}

		if ( is_array( $value ) ) {
			$value = $value['display_name'] ?? null;
		} elseif ( is_numeric( $value ) ) {
			$user  = get_userdata( $value );
			$value = $user ? $user->display_name : null;
		} else {
			$value = $value->data->display_name ?? null;
		}

		return $value;
	}
}
