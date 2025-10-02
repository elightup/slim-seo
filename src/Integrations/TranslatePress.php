<?php
namespace SlimSEO\Integrations;

use WP_Post;
use WP_Term;

class TranslatePress {
	use MultilingualSitemapTrait;
	private $url_converter;
	private $settings;

	public function is_active(): bool {
		return defined( 'TRP_PLUGIN_VERSION' );
	}

	public function setup(): void {
		$trp                 = \TRP_Translate_Press::get_trp_instance();
		$this->url_converter = $trp->get_component( 'url_converter' );
		$this->settings      = $trp->get_component( 'settings' );

		$this->setup_sitemap_hooks();
		add_filter( 'wpseo_sitemap_url', [ $this, 'get_url' ], 0, 2 );  // phpcs:ignore
	}

	public function add_post_links( WP_Post $post ): void {
		$url          = get_permalink( $post );
		$translations = $this->get_translations( $url, $post );
		$this->add_links( $url, $translations );
	}

	public function add_term_links( WP_Term $term ): void {
		$url          = get_term_link( $term );
		$translations = $this->get_translations( $url );
		$this->add_links( $url, $translations );
	}

	public function add_homepage_links(): void {
		$url          = home_url( '/' );
		$translations = $this->get_translations( $url );
		$this->add_links( $url, $translations );
	}

	public function add_post_type_archive_links( string $url ): void {
		$translations = $this->get_translations( $url );
		$this->add_links( $url, $translations );
	}

	private function get_translations( string $url, ?WP_Post $post = null ): array {
		$languages = $this->get_languages();
		$translations = [];

		foreach ( $languages as $language ) {
			$translated_url = apply_filters( 'wpseo_sitemap_url', $url, $language ); // phpcs:ignore
			$translation = [
				'language' => $language,
				'url'      => $translated_url,
			];
			if ( $post ) {
				$translation['lastmod'] = wp_date( 'c', strtotime( $post->post_modified_gmt ) );
			}
			$translations[] = $translation;
		}

		return $translations;
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
