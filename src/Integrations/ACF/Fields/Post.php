<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Post extends Base {
	public function get_value() {
		$value = $this->field['value'];

		if ( empty( $value ) ) {
			return null;
		}

		$post = get_post( $value );

		return $post ? $post->post_title : null;
	}
}
