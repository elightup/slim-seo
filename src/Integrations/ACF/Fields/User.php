<?php
namespace SlimSEO\Integrations\ACF\Fields;

class User extends Base {
	public function get_value() {
		$value = $this->field['value'];

		if ( ! $value ) {
			return null;
		}

		if ( isset( $value['display_name'] ) ) {
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
