<?php
namespace SlimSEO\Migration;

class Replacer {

	/**
	 * Parse other plugins' title and description patterns to text.
	 *
	 * @param string $value pattern.
	 */
	public function replace_post( $post_id ) {
		$this->before_replace_post( $post_id );

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

	public function replace_term( $term_id ) {
		$this->before_replace_term( $term_id );

		$seo_settings = [
			'title'          => $this->get_term_title( $term_id ),
			'description'    => $this->get_term_description( $term_id ),
			'facebook_image' => $this->get_term_facebook_image( $term_id ),
			'twitter_image'  => $this->get_term_twitter_image( $term_id ),
		];
		$seo_settings = array_filter( $seo_settings );
		if ( $seo_settings ) {
			update_term_meta( $term_id, 'slim_seo', $seo_settings );
		}
	}

	public function before_replace_post( $post_id ) {
		return null;
	}

	public function before_replace_term( $term_id ) {
		return null;
	}

	public function cleanup_posts() {
		return null;
	}

	public function cleanup_terms() {
		return null;
	}

	protected function get_post_title( $post_id ) {
		return null;
	}
	protected function get_post_description( $post_id ) {
		return null;
	}
	protected function get_post_facebook_image( $post_id ) {
		return null;
	}
	protected function get_post_twitter_image( $post_id ) {
		return null;
	}

	protected function get_term_title( $term_id ) {
		return null;
	}

	protected function get_term_description( $term_id ) {
		return null;
	}

	protected function get_term_facebook_image( $term_id ) {
		return null;
	}

	protected function get_term_twitter_image( $term_id ) {
		return null;
	}

	public function is_activated() {
		return true;
	}
}

class Test extends \RankMath\Replace_Variables\Manager {
	private $ss_post_id;

	public function ss_set_post( $post_id ) {
		error_log( $post_id );
		$this->ss_post_id = $post_id;
	}

	protected function get_post() {
		$this->post = get_post( $this->ss_post_id );
		error_log( print_r( $this->post, true ) );

		return $this->post;
	}

	public function __construct() {
	}

	public function should_we_setup() {
		return true;
	}

	public function check() {
		print_r( $this->replacements );
	}
}
