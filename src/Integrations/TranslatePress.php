<?php
namespace SlimSEO\Integrations;

class TranslatePress {
	public function setup() {
		if ( ! defined( 'TRP_PLUGIN_VERSION' ) ) {
			return;
		}

		add_action( 'slim_seo_sitemap_post', [ $this, 'add_post_links' ] );
		add_action( 'slim_seo_sitemap_term', [ $this, 'add_term_links' ] );
	}

	public function add_post_links( $post ) {
		$trp = \TRP_Translate_Press::get_trp_instance();
		$url_converter = $trp->get_component( 'url_converter' );

		$languages = $this->get_languages();
		foreach ( $languages as $language ) {
			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $language ),
				esc_url( $url_converter->get_url_for_language( $language, get_permalink( $post->ID ), '') )
			);
		}
	}

	public function add_term_links( $term ) {
		$trp = \TRP_Translate_Press::get_trp_instance();
		$url_converter = $trp->get_component( 'url_converter' );

		$languages = $this->get_languages();
		foreach ( $languages as $language ) {
			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $language ),
				esc_url( $url_converter->get_url_for_language( $language, get_term_link( $term->term_id ), '') )
			);
		}
	}

	private function get_languages() {
		$trp          = \TRP_Translate_Press::get_trp_instance();
		$trp_settings = $trp->get_component( 'settings' );
		$settings     = $trp_settings->get_settings();
		$default      = $settings['default-language'];

		return array_filter( $settings['publish-languages'], function( $language ) use ( $default ) {
			return $default !== $language;
		} );
	}
}
