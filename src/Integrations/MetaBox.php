<?php
namespace SlimSEO\Integrations;

class MetaBox {
	public function setup() {
		if ( ! defined( 'RWMB_VER' ) ) {
			return;
		}

		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
	}

	public function skip_shortcodes( $shortcodes ) {
		$shortcodes = array_merge( $shortcodes, [
			'rwmb_meta',                // Meta Box.
			'mb_frontend_form',         // MB Frontend Submission.
			'mb_frontend_dashboard',
			'mb_user_profile_register', // MB User Profile.
			'mb_user_profile_login',
			'mb_user_profile_info',
			'mb_relationships',         // MB Relationships.
			'mbfp-button',              // MB Favorite Posts.
		] );
		return $shortcodes;
	}
}
