<?php
namespace SlimSEO\Redirection\Api;

abstract class Base {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	abstract public function register_routes();

	public function has_permission() : bool {
		return current_user_can( 'manage_options' );
	}
}
