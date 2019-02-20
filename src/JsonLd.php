<?php
namespace SlimSEO;

class JsonLd {
	public function __construct() {
		// Output structured data types. Breadcrumbs are outputted in Breadcrumb class.
		add_action( 'wp_footer', [ $this, 'output_website_data' ] );
	}

	public function output_website_data() {
		$data = [
			'@context'        => 'https://schema.org',
			'@type'           => 'WebSite',
			'url'             => esc_url( home_url( '/' ) ),
			'name'            => get_bloginfo( 'name' ),
			'potentialAction' => [
				'@type'       => 'SearchAction',
				'target'      => esc_url( home_url( '/' ) ) . '?s={search_term_string}',
				'query-input' => 'required name=search_term_string',
			],
		];
		self::output( $data );
	}

	public static function output( $data ) {
		echo "<script type='application/ld+json'>\n", json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n</script>\n";
	}
}
