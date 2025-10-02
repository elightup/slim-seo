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

	public function get_post_translations( WP_Post $post ): array {
		return $this->get_translations( get_permalink( $post ), $post );
	}

	public function get_term_translations( WP_Term $term ): array {
		return $this->get_translations( get_term_link( $term ) );
	}

	public function get_homepage_translations(): array {
		return $this->get_translations( home_url( '/' ) );
	}

	public function get_post_type_archive_translations( string $url ): array {
		return $this->get_translations( $url );
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
