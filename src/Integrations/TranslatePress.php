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
		add_filter( 'wpseo_sitemap_url', [ $this, 'get_url' ], 0, 2 );  // phpcs:ignore
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
			/**
			 * Hack: TranslatePress checks current filter to bypass translating URLs in sitemaps.
			 * We have to use the Yoast SEO's filter name to make it work.
			 * This will be removed when TranslatePress adds support for Slim SEO's hooks.
			 */
			$translated_url = apply_filters( 'wpseo_sitemap_url', $url, $language ); // phpcs:ignore
			if ( $translated_url === $url ) {
				continue;
			}

			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( str_replace( '_', '-', $language ) ),
				esc_url( $translated_url )
			);
		}
	}

	public function get_url( string $url, string $language ): string {
		return $this->url_converter->get_url_for_language( $language, $url, '' );
	}

	private function get_languages(): array {
		$settings = $this->settings->get_settings();

		return array_diff( $settings['publish-languages'], [ $settings['default-language'] ] );
	}
}
