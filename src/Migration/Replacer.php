<?php
namespace SlimSEO\Migration;

abstract class Replacer {

	/**
	 * Parse other plugins' title and description patterns to text.
	 *
	 * @param string $value pattern.
	 */
	public function replace_post( $post_id ) {
		$seo_settings = [
			'title'          => $this->get_post_title( $post_id ),
			'description'    => $this->get_post_description( $post_id ),
			'facebook_image' => $this->get_post_facebook_image( $post_id ),
			'twitter_image'  => $this->get_post_twitter_image( $post_id ),
		];
		$seo_settings = array_filter( $seo_settings );
		if ( $seo_settings ) {
			update_post_meta( $post_id, 'slim_seo', $seo_settings );
		}
	}

	public function replace_term( $term_id, $term ) {
		$seo_settings = [
			'title'          => $this->get_term_title( $term_id, $term ),
			'description'    => $this->get_term_description( $term_id, $term ),
			'facebook_image' => $this->get_term_facebook_image( $term_id, $term ),
			'twitter_image'  => $this->get_term_twitter_image( $term_id, $term ),
		];
		$seo_settings = array_filter( $seo_settings );
		if ( $seo_settings ) {
			update_term_meta( $term_id, 'slim_seo', $seo_settings );
		}
	}

	abstract function get_post_title( $post_id );
	abstract function get_post_description( $post_id );
	abstract function get_post_facebook_image( $post_id );
	abstract function get_post_twitter_image( $post_id );

	abstract function get_term_title( $term_id, $term );
	abstract function get_term_description( $term_id, $term );
	abstract function get_term_facebook_image( $term_id, $term );
	abstract function get_term_twitter_image( $term_id, $term );

	abstract function is_plugin_activation();
}
