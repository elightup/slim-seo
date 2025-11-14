<?php
namespace SlimSEO\RobotsTxt;

use SlimSEO\Settings\Settings as CommonSettings;

class Loader {
	private $settings = [];

	public function __construct( CommonSettings $settings ) {
		$this->settings = $settings;
	}

	public function setup(): void {
		if ( is_admin() ) {
			new Settings( $this );
		}

		add_filter( 'robots_txt', [ $this, 'robots_txt' ], 9999 );
	}

	public function robots_txt( string $output ): string {
		if ( ! Settings::get( 'robots_txt_editable' ) ) {
			return $this->get_default_content( $output );
		}

		$content = Settings::get( 'robots_txt_content' );

		return $content ?: $this->get_default_content( $output );
	}

	private function get_default_content( string $output ): string {
		if ( $this->settings->is_feature_active( 'meta_robots' ) ) {
			$content  = "Disallow: /?s=\n";
			$content .= "Disallow: /page/*/?s=\n";
			$content .= "Disallow: /search/\n";

			$content = apply_filters( 'slim_seo_robots_txt', $content );
			$output  = str_replace( 'Allow:', "{$content}Allow:", $output );
		}

		if ( $this->settings->is_feature_active( 'sitemaps' ) ) {
			$output .= "\nSitemap: " . home_url( 'sitemap.xml' ) . "\n";
		}

		return $output;
	}

	public function load_default_robots_txt_content(): string {
		remove_filter( 'robots_txt', [ $this, 'robots_txt' ], 9999 );
		ob_start();
		do_robots();
		header( 'Content-Type: text/html; charset=utf-8' );
		$content = $this->get_default_content( ob_get_clean() );
		add_filter( 'robots_txt', [ $this, 'robots_txt' ], 9999 );

		return $content;
	}
}
