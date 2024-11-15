<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Post extends Base {
	public function get_value() {
		$value = $this->field['value'];

		if ( empty( $value ) ) {
			return null;
		}

		$post = is_array( $value ) ? reset( $value ) : $value;
		$post = get_post( $post );

		return $post ? $post->post_title : null;
	}
}
