<?php
namespace SlimSEO;

class Helper {
	public static function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );
		return array_keys( $post_types );
	}
}
