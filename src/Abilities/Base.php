<?php
namespace SlimSEO\Abilities;

abstract class Base {
	public function setup(): void {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		add_action( 'wp_abilities_api_init', [ $this, 'register_abilities' ] );
	}

	abstract public function register_abilities(): void;

	public function check_permission( array $input ) {
		return current_user_can( 'manage_options' );
	}

	protected function input_props(): array {
		return [];
	}

	protected function input_props_required(): array {
		return [];
	}

	protected function input_schema( bool $get = true ): array {
		$schema = [
			'type'                 => 'object',
			'properties'           => $get ? $this->input_props() : array_merge( $this->output_schema( false ), $this->input_props() ),
			'additionalProperties' => false,
		];

		$required = $this->input_props_required();

		if ( $required ) {
			$schema['required'] = $required;
		}

		return $schema;
	}

	abstract protected function output_schema( bool $detailed = true ): array;

	protected function meta( bool $is_readonly = true ): array {
		$annotations = [
			'readonly'    => $is_readonly,
			'destructive' => false,
		];

		if ( ! $is_readonly ) {
			$annotations['idempotent'] = true;
		}

		return [
			'show_in_rest' => true,
			'annotations'  => $annotations,
			'mcp'          => [
				'public'        => true,
				'type'          => 'tool',
				'openWorldHint' => false,
			],
		];
	}

	protected function success_schema(): array {
		return [
			'type'                 => 'object',
			'properties'           => [
				'success' => [
					'type'        => 'boolean',
					'description' => __( 'Whether the data was saved.', 'slim-seo' ),
				],
			],
			'additionalProperties' => false,
		];
	}
}
