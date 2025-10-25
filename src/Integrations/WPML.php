<?php
namespace SlimSEO\Integrations;

use WP_Post;
use WP_Term;

class WPML {
	use MultilingualSitemapTrait;

	public function is_active(): bool {
		return defined( 'ICL_SITEPRESS_VERSION' );
	}

	public function setup(): void {
		$this->setup_sitemap_hooks();

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		do_action( 'wpml_multilingual_options', 'slim_seo' );
		add_filter( 'wpml_tm_adjust_translation_fields', [ $this, 'adjust_fields' ] );
	}

	private function get_post_translations( WP_Post $post ): array {
		return $this->get_translations( get_permalink( $post ), $post->ID, 'post', $post->post_type );
	}

	private function get_term_translations( WP_Term $term ): array {
		return $this->get_translations( get_term_link( $term ), $term->term_id, 'term', $term->taxonomy );
	}

	private function get_homepage_translations(): array {
		$languages    = $this->get_languages();
		$translations = [];
		$home_url     = home_url( '/' );

		foreach ( $languages as $language ) {
			$url = apply_filters( 'wpml_permalink', $home_url, $language, true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			if ( ! $url || $url === $home_url ) {
				continue;
			}
			$translation    = compact( 'language', 'url' );
			$translations[] = $translation;
		}

		return $translations;
	}

	private function get_post_type_archive_translations( string $post_type ): array {
		$languages    = $this->get_languages();
		$translations = [];
		$archive_url  = get_post_type_archive_link( $post_type );

		$current_language = apply_filters( 'wpml_current_language', null );
		foreach ( $languages as $language ) {
			do_action( 'wpml_switch_language', $language );         // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$url = get_post_type_archive_link( $post_type );
			do_action( 'wpml_switch_language', $current_language ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

			// TODO: Uncomment this when WPML is fixed.
			// $url = apply_filters( 'wpml_permalink', $archive_url, $language, true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			if ( ! $url || $url === $archive_url ) {
				continue;
			}
			$translation    = compact( 'language', 'url' );
			$translations[] = $translation;
		}

		return $translations;
	}

	private function get_translations( string $url, int $object_id, string $object_type, string $type ): array {
		$languages    = $this->get_languages();
		$translations = [];

		foreach ( $languages as $language ) {
			$translated_id = apply_filters( 'wpml_object_id', $object_id, $type, true, $language );
			if ( ! $translated_id ) {
				continue;
			}
			$translation = [
				'language' => $language,
				'url'      => apply_filters( 'wpml_permalink', $url, $language, true ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			];
			if ( $object_type === 'post' ) {
				$translation['lastmod'] = get_post_modified_time( 'c', true, $translated_id );
			}
			$translations[] = $translation;
		}

		return $translations;
	}

	private function get_languages(): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
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
}
