<?php
namespace SlimSEO\Integrations;

class WPML {
	public function is_active(): bool {
		return defined( 'ICL_SITEPRESS_VERSION' );
	}

	public function setup() {
		add_action( 'slim_seo_sitemap_post', [ $this, 'add_post_links' ] );
		add_action( 'slim_seo_sitemap_term', [ $this, 'add_term_links' ] );

		do_action( 'wpml_multilingual_options', 'slim_seo' );
		add_filter( 'wpml_tm_adjust_translation_fields', [ $this, 'adjust_fields' ] );
	}

	public function add_post_links( \WP_Post $post ): void {
		$original_url = get_permalink( $post );
		$languages    = $this->get_languages();

		foreach ( $languages as $language ) {
			// @codingStandardsIgnoreLine.
			$post_id = apply_filters( 'wpml_object_id', $post->ID, $post->post_type, false, $language );
			if ( ! $post_id ) {
				continue;
			}

			// @codingStandardsIgnoreLine.
			$url = apply_filters( 'wpml_permalink', $original_url, $language, true );
			if ( $url === $original_url ) {
				continue;
			}

			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $language ),
				esc_url( $url )
			);
		}
	}

	public function add_term_links( \WP_Term $term ): void {
		$original_url = get_term_link( $term );
		$languages    = $this->get_languages();

		foreach ( $languages as $language ) {
			// @codingStandardsIgnoreLine.
			$term_id = apply_filters( 'wpml_object_id', $term->term_id, $term->taxonomy, false, $language );
			if ( ! $term_id ) {
				continue;
			}

			// @codingStandardsIgnoreLine.
			$url = apply_filters( 'wpml_permalink', $original_url, $language, true );
			if ( $url === $original_url ) {
				continue;
			}

			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $language ),
				esc_url( $url )
			);
		}
	}

	private function get_languages() {
		// @codingStandardsIgnoreLine.
		return array_keys( apply_filters( 'wpml_active_languages', null, [ 'skip_missing' => true ] ) );
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
