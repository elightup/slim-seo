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
		$languages = $this->get_languages();
		foreach ( $languages as $language ) {
			$url = trailingslashit( get_home_url() ) . trailingslashit( $language ) . $post->post_name;
			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $language ),
				esc_url( esc_url( $url ) )
			);
		}
	}

	public function add_term_links( $term ) {
		$languages = $this->get_languages();

		foreach ( $languages as $language ) {
			$url = trailingslashit( get_home_url() ) . trailingslashit( $language ) . $term->slug;
			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $language ),
				esc_url( $url )
			);
		}
	}

	private function get_languages() {
		$trp          = \TRP_Translate_Press::get_trp_instance();
		$trp_settings = $trp->get_component( 'settings' );
		$settings     = $trp_settings->get_settings();
		$default      = $settings['default-language'];

		return array_filter( $settings['publish-languages'], function( $language ) use ( $default ) {
			if ( $default === $language ) {
				return false;
			}
			return true;
		} );
	}
}
