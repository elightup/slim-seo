<?php
namespace SlimSEO\Migration;

use RankMath\Helper as RMHelper;

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
		return RMHelper::replace_vars( $title, $post );
	}

	public function get_post_description( $post_id ) {
		$post        = get_post( $post_id, ARRAY_A );
		$description = get_post_meta( $post_id, 'rank_math_description', true );
		return RMHelper::replace_vars( $description, $post );
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

	protected function get_post_noindex( $post_id ) {
		$robots = get_post_meta( $post_id, 'rank_math_robots', true );
		return intval( is_array( $robots ) && in_array( 'noindex', $robots, true ) );
	}

	public function get_term_title( $term_id ) {
		$term = get_term( $term_id );
		if ( ! $term ) {
			return '';
		}
		$title = get_term_meta( $term_id, 'rank_math_title', true );
		return RMHelper::replace_vars( $title, $term );
	}

	public function get_term_description( $term_id ) {
		$term = get_term( $term_id );
		if ( ! $term ) {
			return '';
		}
		$description = get_term_meta( $term_id, 'rank_math_description', true );
		return RMHelper::replace_vars( $description, $term );
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

	protected function get_term_noindex( $term_id ) {
		$robots = get_term_meta( $term_id, 'rank_math_robots', true );
		return intval( is_array( $robots ) && in_array( 'noindex', $robots, true ) );
	}

	public function is_activated() {
		return defined( 'RANK_MATH_VERSION' );
	}
}
