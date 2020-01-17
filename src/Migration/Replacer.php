<?php
namespace SlimSEO\Migration;

abstract class Replacer {

	/**
	 * Parse other plugins' title and description patterns to text.
	 *
	 * @param string $value pattern.
	 */
	public function replace( $post_id ) {
		$new_value = [
			'title'          => $this->get_title( $post_id ),
			'description'    => $this->get_description( $post_id ),
			'facebook_image' => $this->get_facebook_image( $post_id ),
			'twitter_image'  => $this->get_twitter_image( $post_id ),
		];
		update_post_meta( $post_id, 'slim_seo', $new_value );
	}

	abstract function get_title( $post_id );
	abstract function get_description( $post_id );
	abstract function get_facebook_image( $post_id );
	abstract function get_twitter_image( $post_id );
}
