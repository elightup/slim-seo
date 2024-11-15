<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Relationship extends Base {
	public function get_value() {
		$posts = $this->field['value'];

		if ( empty( $posts ) ) {
			return null;
		}

		$post = reset( $posts );
		$post = get_post( $post );

		return $post ? $post->post_title : null;
	}
}
