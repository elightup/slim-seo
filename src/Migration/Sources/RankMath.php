<?php
namespace SlimSEO\Migration\Sources;

use RankMath\Helper as RMHelper;
use SlimSEO\Redirection\Database\Redirects as DbRedirects;
use SlimSEO\Redirection\Helper as RedirectionHelper;
use SlimSEO\RobotsTxt\Settings as RobotsTxtSettings;

class RankMath extends Source {
	protected $constant = 'RANK_MATH_VERSION';

	protected function before_migrate_post( $post_id ) {
		$manager = new RankMathManager();
		$manager->set_post( $post_id );
		$manager->setup();

		rank_math()->variables = $manager;
	}

	protected function before_migrate_term( $term_id ) {
		$manager = new RankMathManager();
		$manager->set_term( $term_id );
		$manager->setup();

		rank_math()->variables = $manager;
	}

	protected function get_post_title( $post_id ) {
		$post  = get_post( $post_id, ARRAY_A );
		$title = (string) get_post_meta( $post_id, 'rank_math_title', true );
		$title = $this->replace_with_slim_seo_variables( $title );

		return RMHelper::replace_vars( $title, $post );
	}

	protected function get_post_description( $post_id ) {
		$post        = get_post( $post_id, ARRAY_A );
		$description = (string) get_post_meta( $post_id, 'rank_math_description', true );
		$description = $this->replace_with_slim_seo_variables( $description );

		return RMHelper::replace_vars( $description, $post );
	}

	protected function get_post_facebook_image( $post_id ) {
		return get_post_meta( $post_id, 'rank_math_facebook_image', true );
	}

	protected function get_post_twitter_image( $post_id ) {
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

	protected function get_term_title( $term_id ) {
		$term = get_term( $term_id );
		if ( ! $term ) {
			return '';
		}
		$title = (string) get_term_meta( $term_id, 'rank_math_title', true );
		$title = $this->replace_with_slim_seo_variables( $title, 'term' );

		return RMHelper::replace_vars( $title, $term );
	}

	protected function get_term_description( $term_id ) {
		$term = get_term( $term_id );
		if ( ! $term ) {
			return '';
		}
		$description = (string) get_term_meta( $term_id, 'rank_math_description', true );
		$description = $this->replace_with_slim_seo_variables( $description, 'term' );

		return RMHelper::replace_vars( $description, $term );
	}

	protected function get_term_facebook_image( $term_id ) {
		return get_term_meta( $term_id, 'rank_math_facebook_image', true );
	}

	protected function get_term_twitter_image( $term_id ) {
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
		$count = 0;
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rank_math_redirections", ARRAY_A );

		if ( empty( $results ) ) {
			return $count;
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
					'enable'           => intval( 'active' === $result['status'] ),
					'ignoreParameters' => 0,
				];

				$db_redirects->update( $redirect );

				++$count;
			}
		}

		return $count;
	}

	public function migrate_robots(): bool {
		$general_option = get_option( 'rank-math-options-general' ) ?: [];

		return RobotsTxtSettings::migrate( $general_option['robots_txt_content'] ?? '' );
	}

	private function replace_with_slim_seo_variables( string $text, string $type = 'post' ): string {
		$variables = [
			'%title%'            => '{{ post.title }}',
			'%excerpt%'          => '{{ post.auto_description }}',
			'%excerpt_only%'     => '{{ post.excerpt }}',
			'%post_thumbnail%'   => '{{ post.thumbnail }}',
			'%date%'             => '{{ post.date }}',
			'%date(F j, Y)%'     => '{{ post.date }}',
			'%date(Y-m-d)%'      => '{{ post.date }}',
			'%date(m/d/Y)%'      => '{{ post.date }}',
			'%date(d/m/Y)%'      => '{{ post.date }}',
			'%modified%'         => '{{ post.modified_date }}',
			'%modified(F j, Y)%' => '{{ post.modified_date }}',
			'%modified(Y-m-d)%'  => '{{ post.modified_date }}',
			'%modified(m/d/Y)%'  => '{{ post.modified_date }}',
			'%modified(d/m/Y)%'  => '{{ post.modified_date }}',
			'%tag%'              => '{{ post.tags }}',
			'%tags%'             => '{{ post.tags }}',
			'%category%'         => '{{ post.categories }}',
			'%categories%'       => '{{ post.categories }}',
			'%term_description%' => '{{ term.description }}',
			'%pt_single%'        => '{{ post_type.singular }}',
			'%pt_plural%'        => '{{ post_type.plural }}',
			'%name%'             => '{{ author.display_name }}',
			'%user_description%' => '{{ author.description }}',
			'%sitename%'         => '{{ site.title }}',
			'%sitedesc%'         => '{{ site.description }}',
			'%currentyear%'      => '{{ current.year }}',
			'%page%'             => '{{ page }}',
			'%sep%'              => '{{ sep }}',
		];

		if ( $type === 'term' ) {
			$variables['%excerpt%'] = '{{ term.auto_description }}';
		}

		return strtr( $text, $variables );
	}
}
