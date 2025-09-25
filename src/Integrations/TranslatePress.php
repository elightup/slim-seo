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
		$extra = [
			'lastmod' => wp_date( 'c', strtotime( $post->post_modified_gmt ) ),
		];
		$this->add_links( get_permalink( $post ), $extra );
	}

	public function add_term_links( \WP_Term $term ): void {
		$this->add_links( get_term_link( $term ) );
	}

	private function add_links( string $url, array $extra = [] ): void {
		$urls = $this->get_all_translation_urls( $url );
		$this->output_all_hreflang_links( $urls );
		echo "\t</url>\n"; // Close the default URL.

		// Google requires each translation to be in a separate <url> element with all the hreflang links.
		$translations = array_values( array_diff( $urls, [ $url ] ) );
		$count        = count( $translations );
		foreach ( $translations as $index => $translation ) {
			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( $translation ), "</loc>\n";

			// Output the extra attributes: lastmod, etc.
			foreach ( $extra as $key => $value ) {
				// Translators: %1$s is the key, %2$s is the value.
				printf( "\t\t<%1\$s>%2\$s</%1\$s>\n", esc_attr( $key ), esc_html( $value ) );
			}

			$this->output_all_hreflang_links( $urls );

			// Do not close the last translation.
			if ( $index < $count - 1 ) {
				echo "\t</url>\n";
			}
		}
	}

	private function output_all_hreflang_links( array $urls ): void {
		// Cache the output to avoid printing the same output multiple times.
		static $output = '';
		if ( $output ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		ob_start();
		$default_url      = '';
		$default_language = $this->get_default_language();

		foreach ( $urls as $language => $url ) {
			$this->output_hreflang_link( $language, $url );

			if ( $language === $default_language ) {
				$default_url = $url;
			}
		}

		if ( $default_url ) {
			$this->output_hreflang_link( 'x-default', $default_url );
		}

		$output = ob_get_clean();
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function output_hreflang_link( string $language, string $url ): void {
		$language = str_replace( '_', '-', $language );

		printf(
			"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
			esc_attr( $language ),
			esc_url( $url )
		);
	}

	private function get_all_translation_urls( string $url ): array {
		$languages = $this->get_languages();
		$urls      = [];

		foreach ( $languages as $language ) {
			$translated_url    = apply_filters( 'wpseo_sitemap_url', $url, $language ); // phpcs:ignore
			$urls[ $language ] = $translated_url;
		}

		return $urls;
	}

	public function get_url( string $url, string $language ): string {
		return $this->url_converter->get_url_for_language( $language, $url, '' );
	}

	private function get_languages(): array {
		$settings = $this->settings->get_settings();

		return $settings['publish-languages'];
	}

	private function get_default_language(): string {
		$settings = $this->settings->get_settings();

		return $settings['default-language'];
	}
}
