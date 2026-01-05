<?php
/**
 * WPML integration
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace SlimSEO\Integrations;

use WP_Post;
use WP_Term;
use WP_Query;
use WPML\Settings\LanguageNegotiation;
use SlimSEO\Helpers\Data as HelpersData;

class WPML {
	use MultilingualSitemapTrait;

	public function is_active(): bool {
		return defined( 'ICL_SITEPRESS_VERSION' );
	}

	public function setup(): void {
		$this->setup_sitemap_hooks();

		do_action( 'wpml_multilingual_options', 'slim_seo' );
		add_filter( 'wpml_tm_adjust_translation_fields', [ $this, 'adjust_fields' ] );

		if ( ! LanguageNegotiation::isDomain() ) {
			add_action( 'parse_query', [ $this, 'remove_sitemap_from_non_default_language' ] );
		}

		add_filter( 'slim_seo_redirection_home_url', [ HelpersData::class, 'multilanguage_home_url' ], 10, 2 );
	}

	private function get_post_translations( WP_Post $post ): array {
		return $this->get_translations( $post->ID, 'post', $post->post_type );
	}

	private function get_term_translations( WP_Term $term ): array {
		return $this->get_translations( $term->term_id, 'term', $term->taxonomy );
	}

	private function get_homepage_translations(): array {
		$languages        = $this->get_languages();
		$translations     = [];
		$base_url         = home_url( '/' );
		$current_language = apply_filters( 'wpml_current_language', null );

		foreach ( $languages as $language ) {
			do_action( 'wpml_switch_language', $language );
			$url = home_url( '/' );
			if ( ! $url || ( $url === $base_url && $language !== $current_language ) ) {
				continue;
			}
			$translation    = compact( 'language', 'url' );
			$translations[] = $translation;
		}

		do_action( 'wpml_switch_language', $current_language );

		return $translations;
	}

	private function get_post_type_archive_translations( string $post_type ): array {
		$languages    = $this->get_languages();
		$translations = [];
		$base_url     = get_post_type_archive_link( $post_type );

		$current_language = apply_filters( 'wpml_current_language', null );

		foreach ( $languages as $language ) {
			do_action( 'wpml_switch_language', $language );
			$url = get_post_type_archive_link( $post_type );
			if ( ! $url || ( $url === $base_url && $language !== $current_language ) ) {
				continue;
			}
			$translation    = compact( 'language', 'url' );
			$translations[] = $translation;
		}

		do_action( 'wpml_switch_language', $current_language );

		return $translations;
	}

	private function get_translations( int $object_id, string $object_type, string $type ): array {
		$languages        = $this->get_languages();
		$translations     = [];
		$current_language = apply_filters( 'wpml_current_language', null );

		foreach ( $languages as $language ) {
			do_action( 'wpml_switch_language', $language );

			$translated_id = apply_filters( 'wpml_object_id', $object_id, $type, true, $language );
			if ( ! $translated_id || ( $object_id === $translated_id && $language !== $current_language ) ) {
				continue;
			}

			$url = $object_type === 'post' ? get_permalink( $translated_id ) : get_term_link( $translated_id );
			if ( ! $url || ! is_string( $url ) ) {
				continue;
			}

			$translation = compact( 'language', 'url' );

			if ( $object_type === 'post' ) {
				$translation['lastmod'] = get_post_modified_time( 'c', true, $translated_id );
			}

			$translations[] = $translation;
		}

		do_action( 'wpml_switch_language', $current_language );

		return $translations;
	}

	private function get_languages(): array {
		return array_keys( apply_filters( 'wpml_active_languages', [], [ 'skip_missing' => true ] ) );
	}

	private function get_default_language(): string {
		return apply_filters( 'wpml_default_language', '' );
	}

	public function adjust_fields( array $fields ): array {
		foreach ( $fields as &$field ) {
			if ( $field['field_type'] === 'field-slim_seo-0-title' ) {
				$field['purpose'] = 'seo_title';
			} elseif ( $field['field_type'] === 'field-slim_seo-0-description' ) {
				$field['purpose'] = 'seo_meta_description';
			}
		}

		return $fields;
	}

	/**
	 * Removes the sitemap query var on non-default languages to avoid generating
	 * wrong sitemaps for non-default languages like /de/sitemap.xml.
	 * This will only run when the language URL format is not per domain.
	 *
	 * @param WP_Query $query The WordPress query object.
	 */
	public function remove_sitemap_from_non_default_language( WP_Query $query ): void {
		$current_language = apply_filters( 'wpml_current_language', null );
		if ( $current_language === $this->get_default_language() || ! $query->get( 'ss_sitemap' ) ) {
			return;
		}
		unset( $query->query_vars['ss_sitemap'] );
		$query->set_404();
		status_header( 404 );
	}
}
