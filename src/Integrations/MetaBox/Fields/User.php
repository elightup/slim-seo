<?php
namespace SlimSEO\Integrations\MetaBox\Fields;

class User extends Base {
	public static function get_single_value( $value ) {
		if ( ! $value ) {
			return null;
		}
		$user = get_userdata( $value );
		return $user ? $user->display_name : null;
	}
}
