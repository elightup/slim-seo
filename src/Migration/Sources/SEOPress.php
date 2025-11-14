<?php
namespace SlimSEO\Migration\Sources;

use SlimSEO\Redirection\Database\Redirects as DbRedirects;
use SlimSEO\Redirection\Helper as RedirectionHelper;
use SlimSEO\RobotsTxt\Settings as RobotsTxtSettings;

class SEOPress extends Source {
	protected $constant = 'SEOPRESS_VERSION';
	private $context;

	private function is_pro(): bool {
		return defined( 'SEOPRESS_PRO_VERSION' );
	}

	protected function before_migrate_post( $post_id ) {
		$this->context = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( $post_id )->getContext();
	}

	protected function get_post_title( $post_id ) {
		return seopress_get_service( 'TitleMeta' )->getValue( $this->context );
	}

	protected function get_post_description( $post_id ) {
		return seopress_get_service( 'DescriptionMeta' )->getValue( $this->context );
	}

	protected function get_post_facebook_image( $post_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		return $social['og']['image'] ?? '';
	}

	protected function get_post_twitter_image( $post_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		return $social['twitter']['image'] ?? '';
	}

	/**
	 * Get meta robots noindex value.
	 *
	 * Must use low-level function `get_post_meta()` because the `RobotMeta` service tries to get the primary category of a post.
	 * In case post type = 'product' and the website doesn't use WooCommerce, this call returns `WP_Error` and breaks the Ajax.
	 *
	 * @link https://github.com/wp-seopress/wp-seopress-public/blob/master/src/Helpers/Metas/RobotSettings.php#L14
	 */
	protected function get_post_noindex( $post_id ): int {
		$noindex = get_post_meta( $post_id, '_seopress_robots_index', true );
		$noindex = $noindex === true || $noindex === 'yes';
		return intval( $noindex );
	}

	protected function before_migrate_term( $term_id ) {
		add_filter( 'seopress_primary_category_list', '__return_empty_array' );
		$term          = get_term( $term_id );
		$this->context = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( $term_id, [
			'type'     => 'term',
			'taxonomy' => $term->taxonomy,
		] )->getContext();
	}

	protected function get_term_title( $term_id ) {
		return seopress_get_service( 'TitleMeta' )->getValue( $this->context );
	}

	protected function get_term_description( $term_id ) {
		return seopress_get_service( 'DescriptionMeta' )->getValue( $this->context );
	}

	protected function get_term_facebook_image( $term_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		return $social['og']['image'] ?? '';
	}

	protected function get_term_twitter_image( $term_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		return $social['twitter']['image'] ?? '';
	}

	protected function get_term_noindex( $term_id ) {
		$robots = seopress_get_service( 'RobotMeta' )->getValue( $this->context );
		return intval( ! empty( $robots['noindex'] ) );
	}

	public function migrate_redirects() {
		$count = 0;
		$posts = get_posts( [
			'post_type'      => 'seopress_404',
			'post_status'    => 'any',
			'posts_per_page' => -1,
		] );

		if ( empty( $posts ) ) {
			return $count;
		}

		$db_redirects   = new DbRedirects();
		$redirect_types = RedirectionHelper::redirect_types();

		foreach ( $posts as $post ) {
			$type     = get_post_meta( $post->ID, '_seopress_redirections_type', true );
			$redirect = [
				'type'             => isset( $redirect_types[ $type ] ) ? $type : 301,
				'condition'        => get_post_meta( $post->ID, '_seopress_redirections_enabled_regex', true ) ? 'regex' : 'exact-match',
				'from'             => $post->post_title,
				'to'               => get_post_meta( $post->ID, '_seopress_redirections_value', true ),
				'note'             => '',
				'enable'           => get_post_meta( $post->ID, '_seopress_redirections_enabled', true ) && 'publish' === $post->post_status ? 1 : 0,
				'ignoreParameters' => 'exact_match' === get_post_meta( $post->ID, '_seopress_redirections_param', true ) ? 0 : 1,
			];

			$db_redirects->update( $redirect );

			++$count;
		}

		return $count;
	}

	public function migrate_robots(): bool {
		if ( ! $this->is_pro() ) {
			return false;
		}

		$option_name         = 'seopress_pro_option_name';
		$enable_setting_name = 'seopress_robots_enable';
		$robots_setting_name = 'seopress_robots_file';

		if ( is_multisite() ) {
			$option_name         = 'seopress_pro_mu_option_name';
			$enable_setting_name = 'seopress_mu_robots_enable';
			$robots_setting_name = 'seopress_mu_robots_file';
		}

		$option = get_option( $option_name ) ?: [];

		if ( empty( $option[ $enable_setting_name ] ) ) {
			return false;
		}

		return RobotsTxtSettings::migrate( $option[ $robots_setting_name ] ?? '' );
	}
}
