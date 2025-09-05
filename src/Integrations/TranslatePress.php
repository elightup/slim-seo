<?php
namespace SlimSEO\Integrations;

class TranslatePress {
	private $url_converter;
	private $settings;

	public function is_active(): bool {
		return defined( 'TRP_PLUGIN_VERSION' );
	}

	public function setup(): void {
		$trp                 = \TRP_Translate_Press::get_trp_instance();
		$this->url_converter = $trp->get_component( 'url_converter' );
		$this->settings      = $trp->get_component( 'settings' );

		add_action( 'slim_seo_sitemap_post', [ $this, 'add_post_links' ] );
		add_action( 'slim_seo_sitemap_term', [ $this, 'add_term_links' ] );
	}

	public function add_post_links( \WP_Post $post ): void {
		$this->add_links( get_permalink( $post ) );
	}

	public function add_term_links( \WP_Term $term ): void {
		$this->add_links( get_term_link( $term ) );
	}

	private function add_links( string $url ): void {
		$languages = $this->get_languages();
		foreach ( $languages as $language ) {
			$translated_url = $this->url_converter->get_url_for_language( $language, $url, '' );
			if ( $translated_url === $url ) {
				continue;
			}

			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $language ),
				esc_url( $translated_url )
			);
		}
	}

	private function get_languages(): array {
		$settings = $this->settings->get_settings();

		return array_diff( $settings['publish-languages'], [ $settings['default-language'] ] );
	}
}
