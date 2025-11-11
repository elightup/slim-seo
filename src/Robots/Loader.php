<?php
namespace SlimSEO\Robots;

use SlimSEO\Settings\Settings as CommonSettings;

class Loader {
	private $settings = [];

	public function __construct( CommonSettings $settings ) {
		$this->settings = $settings;
	}

	public function setup(): void {
		if ( is_admin() ) {
			new Settings();
		}

		add_filter( 'robots_txt', [ $this, 'robots_txt' ], 9999 );
	}

	public function robots_txt( string $output ): string {
		if ( ! Settings::get( 'robots_txt_editable' ) ) {
			return $this->default_robots_txt( $output );
		}

		$robots_txt_content = Settings::get( 'robots_txt_content' );

		return $robots_txt_content ?: $this->default_robots_txt( $output );
	}

	private function default_robots_txt( string $output ): string {
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
}
