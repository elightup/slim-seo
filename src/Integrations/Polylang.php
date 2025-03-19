<?php
namespace SlimSEO\Integrations;

use WP_Post;

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

	public function add_post_links( $post ) {
		$translations = pll_get_post_translations( $post->ID );
		foreach ( $translations as $code => $post_id ) {
			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $code ),
				esc_url( get_permalink( $post_id ) )
			);
		}
	}

	public function add_term_links( $term ) {
		$translations = pll_get_term_translations( $term->term_id );
		foreach ( $translations as $code => $term_id ) {
			printf(
				"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
				esc_attr( $code ),
				esc_url( get_term_link( $term_id ) )
			);
		}
	}

	public function add_language_for_js(): void {
		wp_add_inline_script( 'slim-seo-build-meta-tags', 'var ssLang = "' . $this->get_admin_language() . '";', 'before' );
	}

	private function get_admin_language(): string {
		return PLL()->filter_lang ? PLL()->filter_lang->slug : '';
	}
}
