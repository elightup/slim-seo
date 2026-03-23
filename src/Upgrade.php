<?php
namespace SlimSEO;

class Upgrade {
	public function setup() {
		$version = (int) get_option( 'slim_seo_db_version' );
		if ( $version >= SLIM_SEO_DB_VER ) {
			return;
		}

		$upgrades = [
			1 => 'upgrade_to_v1',
			2 => 'upgrade_to_v2',
		];

		for ( $i = $version + 1; $i <= SLIM_SEO_DB_VER; $i++ ) {
			$method = $upgrades[ $i ] ?? null;
			if ( $method && method_exists( $this, $method ) ) {
				$this->$method();
			}
		}

		update_option( 'slim_seo_db_version', SLIM_SEO_DB_VER );
	}

	private function upgrade_to_v1(): void {
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

	private function upgrade_to_v2(): void {
		$option = get_option( 'slim_seo' ) ?: [];

		if ( ! empty( $option['openai_key'] ) && empty( $option['ai_api_key'] ) ) {
			$option['ai_api_key']  = $option['openai_key'];
			$option['ai_provider'] = 'openai';
			$option['ai_model']    = 'gpt-4.1-mini';

			unset( $option['openai_key'] );

			update_option( 'slim_seo', $option );
		}
	}
}
