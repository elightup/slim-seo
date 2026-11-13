<?php
namespace SlimSEO\Integrations;

use PLL_Language;
use PLL_Translate_Option;
use SlimSEO\Settings\MetaTags\Manager;
use WP_Post;
use WP_Term;

class Polylang {
	use MultilingualSitemapTrait;

	public function is_active(): bool {
		return defined( 'POLYLANG_VERSION' );
	}

	public function setup(): void {
		$this->setup_sitemap_hooks();
		add_filter( 'slim_seo_sitemap_post_type_query_args', [ $this, 'query_all_translations' ] );
		add_filter( 'slim_seo_sitemap_post_ignore', [ $this, 'ignore_translations' ], 10, 2 );
		add_action( 'slim_seo_settings_enqueue', [ $this, 'add_language_for_js' ] );

		add_action( 'init', [ $this, 'register_translatable_options' ], 99 );
	}

	public function register_translatable_options(): void {
		if ( ! class_exists( PLL_Translate_Option::class ) ) {
			return;
		}

		new PLL_Translate_Option(
			'slim_seo',
			$this->get_translatable_fields(),
			[
				'context' => 'Slim SEO',
			]
		);
	}

	/**
	 * Returns the option keys that are allowed for translation.
	 *
	 * Only human-readable content fields are listed. Machine settings (feature
	 * flags, API keys, code snippets, numeric flags) must never be registered:
	 * Polylang replaces registered values per language and lists them in the
	 * string translations table, which would change behavior per language and
	 * expose credentials.
	 */
	private function get_translatable_fields(): array {
		$fields = array_fill_keys( array_keys( Manager::DEFAULTS ), 1 );

		return apply_filters(
			'slim_seo_polylang_translatable_fields',
			[
				'default_facebook_image' => 1,
				'default_twitter_image'  => 1,
			] + array_fill_keys( Manager::get_content_items(), $fields )
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

	private function get_post_translations( WP_Post $post ): array {
		$translations = pll_get_post_translations( $post->ID );
		$return       = [];
		foreach ( $translations as $code => $post_id ) {
			$return[] = [
				'language' => $this->from_code_to_locale( $code ),
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
				'language' => $this->from_code_to_locale( $code ),
				'url'      => get_term_link( $term_id ),
			];
		}

		return $return;
	}

	private function get_homepage_translations(): array {
		$languages = $this->get_languages();
		$return    = [];
		foreach ( $languages as $language ) {
			$return[] = [
				'language' => $language->locale,
				'url'      => pll_home_url( $language->slug ),
			];
		}
		return $return;
	}

	private function get_post_type_archive_translations( string $post_type ): array {
		$languages = $this->get_languages();
		$return    = [];
		foreach ( $languages as $language ) {
			PLL()->curlang = $language; // Switch the language to get the correct archive link.
			$url           = get_post_type_archive_link( $post_type );
			if ( ! $url ) {
				continue;
			}
			$return[] = [
				'language' => $language->locale,
				'url'      => $url,
			];
		}
		return $return;
	}

	/**
	 * Get list of language objects. Need to pass empty 'fields' parameter to get the objects.
	 *
	 * @return PLL_Language[]
	 */
	private function get_languages(): array {
		return wp_list_filter( PLL()->model->get_languages_list(), [ 'active' => false ], 'NOT' );
	}

	private function get_default_language(): string {
		return pll_default_language( 'locale' );
	}

	private function from_code_to_locale( string $code ): string {
		static $map = [];
		if ( ! empty( $map ) ) {
			return $map[ $code ] ?? '';
		}

		$languages = $this->get_languages();
		foreach ( $languages as $language ) {
			$map[ $language->slug ] = $language->locale;
		}

		return $map[ $code ] ?? '';
	}

	public function add_language_for_js(): void {
		wp_add_inline_script( 'slim-seo-build-meta-tags', 'var ssLang = "' . $this->get_admin_language() . '";', 'before' );
	}

	private function get_admin_language(): string {
		return PLL()->filter_lang ? PLL()->filter_lang->slug : '';
	}
}
