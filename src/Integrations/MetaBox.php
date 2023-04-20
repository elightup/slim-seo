<?php
namespace SlimSEO\Integrations;

class MetaBox {
	public function setup() {
		if ( ! defined( 'RWMB_VER' ) ) {
			return;
		}

		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
		add_filter( 'slim_seo_skipped_blocks', [ $this, 'skip_blocks' ] );
	}

	public function skip_shortcodes( array $shortcodes ): array {
		return array_merge( $shortcodes, [
			'rwmb_meta',                // Meta Box.
			'mb_frontend_form',         // MB Frontend Submission.
			'mb_frontend_dashboard',
			'mb_user_profile_register', // MB User Profile.
			'mb_user_profile_login',
			'mb_user_profile_info',
			'mb_relationships',         // MB Relationships.
			'mbfp-button',              // MB Favorite Posts.
		] );
	}

	public function skip_blocks( array $blocks ): array {
		return array_merge( $blocks, [
			'meta-box/submission-form',   // MB Frontend Submission.
			'meta-box/user-dashboard',
			'meta-box/login-form',        // MB User Profile.
			'meta-box/profile-form',
			'meta-box/registration-form',
		] );
	}
}
