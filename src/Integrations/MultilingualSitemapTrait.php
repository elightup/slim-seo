<?php
namespace SlimSEO\Integrations;

use WP_Post;
use WP_Term;

trait MultilingualSitemapTrait {
	private function setup_sitemap_hooks(): void {
		$types = [ 'post', 'term', 'homepage', 'post_type_archive' ];
		foreach ( $types as $type ) {
			add_action( "slim_seo_sitemap_$type", [ $this, "add_{$type}_links" ] );
		}
	}

	public function add_post_links( WP_Post $post ): void {
		$url          = get_permalink( $post );
		$translations = $this->get_post_translations( $post );
		$this->add_links( $url, $translations );
	}

	public function add_term_links( WP_Term $term ): void {
		$url          = get_term_link( $term );
		$translations = $this->get_term_translations( $term );
		$this->add_links( $url, $translations );
	}

	public function add_homepage_links(): void {
		$url          = home_url( '/' );
		$translations = $this->get_homepage_translations();
		$this->add_links( $url, $translations );
	}

	public function add_post_type_archive_links( string $post_type ): void {
		$url          = get_post_type_archive_link( $post_type );
		$translations = $this->get_post_type_archive_translations( $post_type );
		$this->add_links( $url, $translations );
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
}