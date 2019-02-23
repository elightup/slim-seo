<?php
namespace SlimSEO;

class JsonLd {
	public function __construct() {
		// Output structured data types. Breadcrumbs are outputted in Breadcrumb class.
		add_action( 'wp_footer', [ $this, 'output_sitelinks_searchbox' ] );
	}

	/**
	 * Output sitelinks searchbox.
	 * @see https://developers.google.com/search/docs/data-types/sitelinks-searchbox
	 */
	public function output_sitelinks_searchbox() {
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

	/**
	 * Output Json-LD data.
	 * Making it static to be used in other modules.
	 */
	public static function output( $data ) {
		echo "<script type='application/ld+json'>\n", json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n</script>\n";
	}
}
