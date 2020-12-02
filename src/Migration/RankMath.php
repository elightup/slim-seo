<?php
namespace SlimSEO\Migration;

class RankMath extends Replacer {
	public function before_replace_post( $post_id ) {
		$manager = new RankMathManager();
		$manager->set_post( $post_id );
		$manager->setup();

		rank_math()->variables = $manager;
	}

	public function before_replace_term( $term_id ) {
		$manager = new RankMathManager();
		$manager->set_term( $term_id );
		$manager->setup();

		rank_math()->variables = $manager;
	}

	public function get_post_title( $post_id ) {
		$post  = get_post( $post_id, ARRAY_A );
		$title = get_post_meta( $post_id, 'rank_math_title', true );
		return \RankMath\Helper::replace_vars( $title, $post );
	}

	public function get_post_description( $post_id ) {
		$post  = get_post( $post_id, ARRAY_A );
		$description = get_post_meta( $post_id, 'rank_math_description', true );
		return \RankMath\Helper::replace_vars( $description, $post );
	}

	public function get_post_facebook_image( $post_id ) {
		return get_post_meta( $post_id, 'rank_math_facebook_image', true );
	}

	public function get_post_twitter_image( $post_id ) {
		$use_facebook_image = get_post_meta( $post_id, 'rank_math_twitter_use_facebook', true );
		if ( $use_facebook_image === 'on' ) {
			return $this->get_post_facebook_image( $post_id );
		}
		return get_post_meta( $post_id, 'rank_math_twitter_image', true );
	}

	public function get_term_title( $term_id ) {
		$term = get_term( $term_id );
		if ( ! $term ) {
			return '';
		}
		$title = get_term_meta( $term_id, 'rank_math_title', true );
		return \RankMath\Helper::replace_vars( $title, $term );
	}

	public function get_term_description( $term_id ) {
		$term = get_term( $term_id );
		if ( ! $term ) {
			return '';
		}
		$description = get_term_meta( $term_id, 'rank_math_description', true );
		return \RankMath\Helper::replace_vars( $description, $term );
	}

	public function get_term_facebook_image( $term_id ) {
		return get_term_meta( $term_id, 'rank_math_facebook_image', true );
	}

	public function get_term_twitter_image( $term_id ) {
		$use_facebook_image = get_term_meta( $term_id, 'rank_math_twitter_use_facebook', true );
		if ( $use_facebook_image === 'on' ) {
			return $this->get_term_facebook_image( $term_id );
		}
		return get_term_meta( $term_id, 'rank_math_twitter_image', true );
	}

	public function is_activated() {
		return defined( 'RANK_MATH_VERSION' );
	}
}

class RankMathManager extends \RankMath\Replace_Variables\Manager {
	private $post_id;
	private $term_id;

	public function set_post( $post_id ) {
		$this->post_id = $post_id;
	}

	public function set_term( $term_id ) {
		$this->term_id = $term_id;
	}

	protected function get_post() {
		$this->post = get_post( $this->post_id );
		return $this->post;
	}

	public function get_term() {
		$term = get_term( $this->term_id );
		return $term->name;
	}

	public function should_we_setup() {
		return true;
	}
}
