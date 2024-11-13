<?php
namespace SlimSEO\Integrations\MetaBox\Fields;

class Post extends Base {
	public static function get_single_value( $value ) {
		if ( ! $value ) {
			return null;
		}
		$post = get_post( $value );
		return $post ? $post->post_title : null;
	}
}
