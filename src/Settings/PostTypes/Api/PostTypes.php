<?php
namespace SlimSEO\Settings\PostTypes\Api;
use WP_REST_Server;
use WP_REST_Request;

use SlimSEO\Helpers\Data;
use MetaBox;

class PostTypes extends Base {
	public function register_routes() {
		register_rest_route( 'slim-seo-post-types', 'option', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_option' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function get_option( WP_REST_Request $request ): array {
		$exclude = array_fill_keys( [
			'home',
			'auto_redirection',
			'enable_404_logs',
			'features',
			'footer_code',
			'force_trailing_slash',
			'header_code',
			'notification_dismissed',
			'default_facebook_image',
			'default_twitter_image',
			'facebook_app_id',
			'twitter_site',
			'default_linkedin_image',
		], '' );
		return array_diff_key( get_option( 'slim_seo' ), $exclude );
	}
}
