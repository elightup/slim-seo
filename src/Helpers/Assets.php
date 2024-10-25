<?php
namespace SlimSEO\Helpers;

class Assets {
	public static function enqueue_build_js( string $name, string $localized_object = '', array $localized_data = [] ): void {
		$asset      = [
			'dependencies' => [],
			'version'      => filemtime( SLIM_SEO_DIR . "js/build/$name.js" ),
		];
		$asset_file = SLIM_SEO_DIR . "js/build/$name.asset.php";
		if ( file_exists( $asset_file ) ) {
			$asset = require $asset_file;
		}

		$handle = "slim-seo-build-$name";
		wp_enqueue_script( $handle, SLIM_SEO_URL . "js/build/$name.js", $asset['dependencies'], $asset['version'], true );
		wp_set_script_translations( $handle, 'slim-seo', SLIM_SEO_DIR . 'languages/' );

		if ( $localized_object ) {
			wp_localize_script( $handle, $localized_object, $localized_data );
		}
	}
}
