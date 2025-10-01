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
		add_action( 'slim_seo_sitemap_post', [ $this, 'add_post_links' ] );
		add_action( 'slim_seo_sitemap_term', [ $this, 'add_term_links' ] );

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		do_action( 'wpml_multilingual_options', 'slim_seo' );
		add_filter( 'wpml_tm_adjust_translation_fields', [ $this, 'adjust_fields' ] );
	}

	public function add_post_links( WP_Post $post ): void {
		$url          = get_permalink( $post );
		$translations = $this->get_translations( $url, $post->ID, 'post', $post->post_type );
		$this->add_links( $url, $translations );
	}

	public function add_term_links( WP_Term $term ): void {
		$url          = get_term_link( $term );
		$translations = $this->get_translations( $url, $term->term_id, 'term', $term->taxonomy );
		$this->add_links( $url, $translations );
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
