<?php
namespace SlimSEO\Integrations;

class WPML {
	public function setup() {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return;
		}

		add_action( 'slim_seo_sitemap_post', [ $this, 'add_post_links' ] );
		add_action( 'slim_seo_sitemap_term', [ $this, 'add_term_links' ] );
	}

	public function add_post_links( $post ) {
		$languages = $this->get_languages();

		foreach ( $languages as $language ) {
			$post_id = apply_filters( 'wpml_object_id', $post->ID, $post->post_type, false, $language );
			if ( ! $post_id ) {
				continue;
			}
			$url = apply_filters( 'wpml_permalink', get_permalink( $post_id ), $language, true );
			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				$language,
				$url
			);
		}
	}

	public function add_term_links( $term ) {
		$languages = $this->get_languages();

		foreach ( $languages as $language ) {
			$term_id = apply_filters( 'wpml_object_id', $term->term_id, $term->taxonomy, false, $language );
			if ( ! $term_id ) {
				continue;
			}
			$url = apply_filters( 'wpml_permalink', get_term_link( $term_id ), $language, true );
			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				$language,
				$url
			);
		}
	}

	private function get_languages() {
		return array_keys( apply_filters( 'wpml_active_languages', null, ['skip_missing' => true] ) );
	}
}