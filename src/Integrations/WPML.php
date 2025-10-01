<?php
namespace SlimSEO\Integrations;

class WPML {
	public function is_active(): bool {
		return defined( 'ICL_SITEPRESS_VERSION' );
	}

	public function setup() {
		add_action( 'slim_seo_sitemap_post', [ $this, 'add_post_links' ] );
		add_action( 'slim_seo_sitemap_term', [ $this, 'add_term_links' ] );

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		do_action( 'wpml_multilingual_options', 'slim_seo' );
		add_filter( 'wpml_tm_adjust_translation_fields', [ $this, 'adjust_fields' ] );
	}

	public function add_post_links( \WP_Post $post ): void {
		$this->add_links( get_permalink( $post ), $post->ID, 'post', $post->post_type );
	}

	public function add_term_links( \WP_Term $term ): void {
		$this->add_links( get_term_link( $term ), $term->term_id, 'term', $term->taxonomy );
	}

	private function add_links( string $url, int $object_id, string $object_type, string $type ): void {
		$translations   = $this->get_translations( $url, $object_id, $object_type, $type );
		$hreflang_links = $this->get_hreflang_links( $translations );
		echo $hreflang_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "\t</url>\n";    // Close the default URL.

		// Google requires each translation to be in a separate <url> element with all the hreflang links.
		$translations = array_filter( $translations, function( array $translation ) use ( $url ): bool {
			return $translation['url'] !== $url;
		} );
		$translations = array_values( $translations );
		$count        = count( $translations );
		foreach ( $translations as $index => $translation ) {
			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( $translation['url'] ), "</loc>\n";

			unset( $translation['language'], $translation['url'] );

			// Output the extra attributes: lastmod, etc.
			foreach ( $translation as $key => $value ) {
				printf( "\t\t<%1\$s>%2\$s</%1\$s>\n", esc_html( $key ), esc_html( $value ) );
			}

			echo $hreflang_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Do not close the last translation.
			if ( $index < $count - 1 ) {
				echo "\t</url>\n";
			}
		}
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

	private function get_hreflang_links( array $translations ): string {
		$links            = '';
		$default_url      = '';
		$default_language = $this->get_default_language();

		foreach ( $translations as $translation ) {
			$links .= $this->get_hreflang_link( $translation['language'], $translation['url'] );

			if ( $translation['language'] === $default_language ) {
				$default_url = $translation['url'];
			}
		}

		if ( $default_url ) {
			$links .= $this->get_hreflang_link( 'x-default', $default_url );
		}

		return $links;
	}

	private function get_hreflang_link( string $language, string $url ): string {
		$language = str_replace( '_', '-', $language );

		return sprintf(
			"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
			esc_attr( $language ),
			esc_url( $url )
		);
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
