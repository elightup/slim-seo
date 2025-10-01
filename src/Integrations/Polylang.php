<?php
namespace SlimSEO\Integrations;

use WP_Post;
use WP_Term;

class Polylang {
	public function is_active(): bool {
		return defined( 'POLYLANG_VERSION' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_sitemap_post_type_query_args', [ $this, 'query_all_translations' ] );
		add_filter( 'slim_seo_sitemap_post_ignore', [ $this, 'ignore_translations' ], 10, 2 );
		add_action( 'slim_seo_sitemap_post', [ $this, 'add_post_links' ] );
		add_action( 'slim_seo_sitemap_term', [ $this, 'add_term_links' ] );
		add_action( 'slim_seo_settings_enqueue', [ $this, 'add_language_for_js' ] );

		// Register translatable options
		new \PLL_Translate_Option(
			'slim_seo',
			[
				'*' => 1, // Translate all fields
			],
			[
				'context' => 'Slim SEO',
			]
		);
	}

	public function query_all_translations( array $args ): array {
		$args['lang'] = 'all';
		return $args;
	}

	public function ignore_translations( bool $ignore, WP_Post $post ): bool {
		$origin         = pll_get_post( $post->ID );
		$is_translation = $origin && $origin !== $post->ID;

		return $ignore || $is_translation;
	}

	public function add_post_links( WP_Post $post ): void {
		$translations = $this->get_post_translations( $post );
		$this->add_links( get_permalink( $post ), $translations );
	}

	public function add_term_links( WP_Term $term ): void {
		$translations = $this->get_term_translations( $term );
		$this->add_links( get_term_link( $term ), $translations );
	}

	private function add_links( string $url, array $translations ): void {
		$hreflang_links = $this->get_hreflang_links( $translations );
		echo $hreflang_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Google requires each translation to be in a separate <url> element with all the hreflang links.
		$translations = array_filter( $translations, function( array $translation ) use ( $url ): bool {
			return $translation['url'] !== $url;
		} );
		if ( empty( $translations ) ) {
			return;
		}

		echo "\t</url>\n"; // Close the default URL.

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

	private function get_post_translations( WP_Post $post ): array {
		$translations = pll_get_post_translations( $post->ID );
		$return       = [];
		foreach ( $translations as $code => $post_id ) {
			$return[] = [
				'language' => $code,
				'url'      => get_permalink( $post_id ),
				'lastmod'  => get_post_modified_time( 'c', true, $post_id ),
			];
		}

		return $return;
	}

	private function get_term_translations( WP_Term $term ): array {
		$translations = pll_get_term_translations( $term->term_id );
		$return       = [];
		foreach ( $translations as $code => $term_id ) {
			$return[] = [
				'language' => $code,
				'url'      => get_term_link( $term_id ),
			];
		}

		return $return;
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
		return pll_languages_list( [ 'fields' => 'locale' ] );
	}

	private function get_default_language(): string {
		return pll_default_language( 'locale' );
	}

	public function add_language_for_js(): void {
		wp_add_inline_script( 'slim-seo-build-meta-tags', 'var ssLang = "' . $this->get_admin_language() . '";', 'before' );
	}

	private function get_admin_language(): string {
		return PLL()->filter_lang ? PLL()->filter_lang->slug : '';
	}
}
