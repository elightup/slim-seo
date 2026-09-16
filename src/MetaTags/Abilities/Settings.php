<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;
use eLightUp\SlimSEO\Common\Helpers\Data as CommonHelpersData;
use SlimSEO\Helpers\Data;

class Settings extends Base {
	protected $object_type = 'settings';

	public function register_abilities(): void {
		wp_register_ability( 'slim-seo/get-default-meta-tags', [
			'category'            => 'slim-seo',
			'label'               => __( 'Get default meta tags settings', 'slim-seo' ),
			'description'         => __( 'Retrieve default meta tags settings (homepage, post types, taxonomies, archives). Returns title, description, and noindex for each context.', 'slim-seo' ),
			'input_schema'        => $this->input_schema(),
			'output_schema'       => $this->output_schema(),
			'meta'                => [
				'show_in_rest' => true,
				'annotations'  => [
					'readonly'      => true,
					'destructive'   => false,
					'openWorldHint' => false,
				],
				'mcp'          => [
					'public' => true,
					'type'   => 'tool',
				],
			],
			'permission_callback' => function ( $input ) {
				return $this->check_permission( $input );
			},
			'execute_callback'    => function ( array $input ): array {
				return $this->get_data( $input );
			},
		] );

		wp_register_ability( 'slim-seo/update-default-meta-tags', [
			'category'            => 'slim-seo',
			'label'               => __( 'Update default meta tags settings', 'slim-seo' ),
			'description'         => __( 'Update default meta tags settings. Only provided fields are updated.', 'slim-seo' ),
			'input_schema'        => $this->input_schema( false ),
			'output_schema'       => [
				'type'                 => 'object',
				'properties'           => [
					'success' => [
						'type'        => 'boolean',
						'description' => __( 'Whether the settings were saved.', 'slim-seo' ),
					],
				],
				'additionalProperties' => false,
			],
			'meta'                => [
				'show_in_rest' => true,
				'annotations'  => [
					'readonly'    => false,
					'destructive' => false,
					'idempotent'  => true,
				],
				'mcp'          => [
					'public' => true,
					'type'   => 'tool',
				],
			],
			'permission_callback' => function ( $input ) {
				return $this->check_permission( $input );
			},
			'execute_callback'    => function ( array $input ): array {
				return $this->update_data( $input );
			},
		] );
	}

	protected function input_props(): array {
		return [
			'context' => [
				'type'        => 'string',
				'description' => __( 'The settings context to retrieve. Use "home" for homepage, a post type slug (e.g. "post", "page") for post types, a post type slug suffixed with "_archive" (e.g. "movie_archive") for archives, or a taxonomy slug (e.g. "category", "post_tag") for taxonomies.', 'slim-seo' ),
			],
		];
	}

	private function get_data( array $input ): array|WP_Error {
		$context      = sanitize_key( wp_unslash( $input['context'] ?? '' ) );
		$context_type = $this->get_context_type( $context );

		if ( empty( $context_type ) ) {
			return new WP_Error( 'slim_seo_abilities_invalid_context', __( 'Invalid settings context.', 'slim-seo' ) );
		}

		$settings = $this->get_object_data();
		$data     = $settings[ $context ] ?? [];
		$data     = ! empty( $data ) ? $data : Helper::get_default_data( $context_type );

		return $this->normalize_data( $data );
	}

	private function update_data( array $input ): array|WP_Error {
		$context      = sanitize_key( wp_unslash( $input['context'] ?? '' ) );
		$context_type = $this->get_context_type( $context );

		if ( empty( $context_type ) ) {
			return new WP_Error( 'slim_seo_abilities_invalid_context', __( 'Invalid settings context.', 'slim-seo' ) );
		}

		$settings = $this->get_object_data();
		$data     = $this->sanitize_data( $settings[ $context ] ?? [], $input );

		if ( empty( $data ) ) {
			unset( $settings[ $context ] );
		} else {
			$settings[ $context ] = $data;
		}

		update_option( 'slim_seo', $settings );

		return [ 'success' => true ];
	}

	protected function get_object_data(): array {
		return get_option( 'slim_seo', [] );
	}

	private function get_context_type( string $context ): string {
		if ( empty( $context ) ) {
			return '';
		}

		if ( in_array( $context, [ 'home', 'author' ], true ) ) {
			return $context;
		}

		$post_types = Data::get_meta_box_post_types();

		if ( in_array( $context, $post_types, true ) ) {
			return 'post';
		}

		$taxonomies = array_keys( CommonHelpersData::get_taxonomies() );

		if ( in_array( $context, $taxonomies, true ) ) {
			return 'term';
		}

		$archives = array_map( fn( $post_type ) => "{$post_type}_archive", $post_types );

		if ( in_array( $context, $archives, true ) ) {
			return 'post_archive';
		}

		return '';
	}
}
