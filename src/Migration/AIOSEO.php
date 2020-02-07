<?php
namespace SlimSEO\Migration;

class AIOSEO extends Replacer {

	public function get_post_title( $post_id ) {
		$title = get_post_meta( $post_id, '_aioseop_title', true );
		return $title;
	}

	public function get_post_description( $post_id ) {
		$description = get_post_meta( $post_id, '_aioseop_description', true );
		return $description;
	}

	public function is_activated() {
		return defined( 'AIOSEOP_VERSION' );
	}
}
