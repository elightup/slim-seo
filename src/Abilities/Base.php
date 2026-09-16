<?php
namespace SlimSEO\Abilities;

use WP_Error;

abstract class Base {
	public function setup() {
		add_action( 'wp_abilities_api_init', [ $this, 'register_abilities' ] );
	}

	public function register_abilities(): void {}

	protected function check_permission( array $input ): bool {
		return current_user_can( 'manage_options' );
	}

	protected function input_props(): array {
		return [];
	}

	protected function input_schema( bool $get = true ): array {
		$input_props = $this->input_props();

		return [
			'type'                 => 'object',
			'properties'           => $get ? $input_props : array_merge( $this->output_schema( false ), $input_props ),
			'additionalProperties' => false,
		];
	}

	protected function output_schema( bool $detailed = true ): array {
		return [];
	}
}
