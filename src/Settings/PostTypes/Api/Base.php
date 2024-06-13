<?php
namespace SlimSEO\Settings\PostTypes\Api;

abstract class Base {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	abstract public function register_routes();

	public function has_permission() {
		return current_user_can( 'edit_posts' );
	}
}
