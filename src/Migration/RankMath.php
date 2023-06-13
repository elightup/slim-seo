<?php
namespace SlimSEO\Migration;

use RankMath\Helper as RMHelper;
use SlimSEO\Redirection\Database\Redirects as DbRedirects;
use SlimSEO\Redirection\Helper as RedirectionHelper;

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

	public function migrate_redirects() {
		$migrated_redirects = 0;

		global $wpdb;

		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rank_math_redirections", ARRAY_A );

		if ( empty( $results ) ) {
			return $migrated_redirects;
		}

		$db_redirects        = new DbRedirects();
		$redirect_types      = RedirectionHelper::redirect_types();
		$redirect_conditions = RedirectionHelper::condition_options();

		foreach ( $results as $result ) {
			if ( empty( $result['sources'] ) ) {
				continue;
			}

			$sources = maybe_unserialize( $result['sources'] );

			if ( ! is_array( $sources ) ) {
				continue;
			}

			foreach ( $sources as $source ) {
				// Ignore if From URL exists
				if ( $db_redirects->exists( $source['pattern'] ) ) {
					continue;
				}

				$type      = $result['header_code'];
				$type      = isset( $redirect_types[ $type ] ) ? $type : 301;
				$condition = $source['comparison'];

				switch ( $condition ) {
					case 'start':
						$condition = 'start-with';

						break;

					case 'end':
						$condition = 'end-with';

						break;

					default:
						$condition = isset( $redirect_conditions[ $condition ] ) ? $condition : 'exact-match';

						break;
				}

				$redirect = [
					'type'             => $type,
					'condition'        => $condition,
					'from'             => $source['pattern'],
					'to'               => $result['url_to'],
					'note'             => '',
					'enable'           => 'active' === $result['status'] ? 1 : 0,
					'ignoreParameters' => 0,
				];

				$db_redirects->update( $redirect );

				$migrated_redirects++;
			}			
		}

		return $migrated_redirects;
	}

	public function is_activated() {
		return defined( 'RANK_MATH_VERSION' );
	}
}
