<?php
namespace SlimSEO\Integrations;

class Polylang {
	public function setup() {
		if ( ! defined( 'POLYLANG_VERSION' ) ) {
			return;
		}

		add_action( 'slim_seo_sitemap_post', array( $this, 'add_post_links' ) );
		add_action( 'slim_seo_sitemap_term', array( $this, 'add_term_links' ) );
	}

	public function add_post_links( $post ) {
		$translations = pll_get_post_translations( $post->ID );
		foreach ( $translations as $code => $post_id ) {
			printf(
				/* translators: 1: hreflang 2: href */
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%1$s\" href=\%2$s\"/>\n",
				$code,
				get_permalink( $post_id )
			);
		}
	}

	public function add_term_links( $term ) {
		$translations = pll_get_term_translations( $term->term_id );
		foreach ( $translations as $code => $term_id ) {
			printf(
				/* translators: 1: hreflang 2: href */
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%1$s\" href=\"%2$s\"/>\n",
				$code,
				get_term_link( $term_id )
			);
		}
	}
}
