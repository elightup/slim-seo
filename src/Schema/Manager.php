<?php
namespace SlimSEO\Schema;

class Manager {
	public function __construct() {
		add_action( 'wp_footer', [ $this, 'output' ] );
	}

	public function output() {
		if ( false === apply_filters( 'slim_seo_schema_enable', true ) ) {
			return;
		}
		$types = apply_filters( 'slim_seo_schema_types', [] );
		if ( empty( $types ) ) {
			return;
		}
		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => array_values( $types ),
		];
		echo "<script type='application/ld+json'>\n", json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n</script>\n";
	}
}
