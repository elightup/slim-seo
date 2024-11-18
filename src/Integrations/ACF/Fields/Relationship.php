<?php
namespace SlimSEO\Integrations\ACF\Fields;

class Relationship extends Base {
	public function get_value() {
		$post = $this->field['value'];

		if ( empty( $post ) ) {
			return null;
		}

		$post = get_post( $post );

		return $post ? $post->post_title : null;
	}
}
