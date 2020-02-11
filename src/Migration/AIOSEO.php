<?php
namespace SlimSEO\Migration;

class AIOSEO extends Replacer {

	public function get_post_title( $post_id ) {
		return get_post_meta( $post_id, '_aioseop_title', true );
	}

	public function get_post_description( $post_id ) {
		return get_post_meta( $post_id, '_aioseop_description', true );
	}

	public function get_post_facebook_image( $post_id ) {
		$settings = $this->get_opengraph_setting( $post_id );
		return $settings ? $this->get_opengraph_image( $settings ) : '';
	}

	public function get_post_twitter_image( $post_id ) {
		$settings = $this->get_opengraph_setting( $post_id );
		// if custom image for twitter is set, return it.
		if ( $settings && $settings['aioseop_opengraph_settings_customimg_twitter'] ) {
			return $settings['aioseop_opengraph_settings_customimg_twitter'];
		}
		// else return opengraph image.
		return $this->get_opengraph_image( $settings );
	}

	public function get_opengraph_setting( $post_id ) {
		return get_post_meta( $post_id, '_aioseop_opengraph_settings', true );
	}

	public function get_opengraph_image( $settings ) {
		return $settings['aioseop_opengraph_settings_image'] ? $settings['aioseop_opengraph_settings_image'] : $settings['aioseop_opengraph_settings_customimg'];
	}

	public function delete_post_meta( $post_id ) {
		delete_post_meta( $post_id, '_aioseop_title' );
		delete_post_meta( $post_id, '_aioseop_description' );
		delete_post_meta( $post_id, '_aioseop_opengraph_settings' );
	}
}
