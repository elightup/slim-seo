<?php
namespace SlimSEO;

class Upgrade {
	public function setup() {
		$version = (int) get_option( 'slim_seo_db_version' );
		if ( $version >= SLIM_SEO_DB_VER ) {
			return;
		}

		for ( $i = 1; $i <= SLIM_SEO_DB_VER; $i++ ) {
			$method = "upgrade_to_v{$i}";
			$this->$method();
		}

		update_option( 'slim_seo_db_version', SLIM_SEO_DB_VER );
	}

	private function upgrade_to_v1() {
		// Upgrade data for homepage settings.
		$option        = get_option( 'slim_seo' ) ?: [];
		$home_settings = array_filter( [
			'title'          => $option['home_title'] ?? '',
			'description'    => $option['home_description'] ?? '',
			'facebook_image' => $option['home_facebook_image'] ?? '',
			'twitter_image'  => $option['home_twitter_image'] ?? '',
		] );

		unset( $option['home_title'], $option['home_description'], $option['home_facebook_image'], $option['home_twitter_image'] );
		if ( $home_settings ) {
			$option['home'] = $home_settings;
		}

		update_option( 'slim_seo', $option );
	}
}
