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
		$settings = $this->get_opengraph_settings( $post_id );
		return empty( $settings['aioseop_opengraph_settings_image'] ) ? '' : $settings['aioseop_opengraph_settings_image'];
	}

	public function get_post_twitter_image( $post_id ) {
		$settings = $this->get_opengraph_settings( $post_id );
		return empty( $settings['aioseop_opengraph_settings_customimg_twitter'] ) ? '' : $settings['aioseop_opengraph_settings_customimg_twitter'];
	}

	public function get_opengraph_settings( $post_id ) {
		return get_post_meta( $post_id, '_aioseop_opengraph_settings', true );
	}

	public function cleanup_posts() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ('_aioseop_title', '_aioseop_description', '_aioseop_opengraph_settings')" );
	}
}
