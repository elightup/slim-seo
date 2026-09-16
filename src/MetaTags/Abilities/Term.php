<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;

class Term extends Base {
	protected $object_type = 'term';
	protected $object_id   = 0;

	public function register_abilities(): void {
		wp_register_ability( 'slim-seo/get-term-meta-tags', [
			'category'            => 'slim-seo',
			'label'               => __( 'Get term meta tags data', 'slim-seo' ),
			'description'         => __( 'Retrieve meta tags for a taxonomy term (title, description, social images, canonical URL, noindex). Provide id, slug, or name.', 'slim-seo' ),
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

		wp_register_ability( 'slim-seo/update-term-meta-tags', [
			'category'            => 'slim-seo',
			'label'               => __( 'Update term meta tags data', 'slim-seo' ),
			'description'         => __( 'Update meta tags for a taxonomy term. Provide id, slug, or name. Only provided fields are updated; others remain unchanged.', 'slim-seo' ),
			'input_schema'        => $this->input_schema( false ),
			'output_schema'       => [
				'type'                 => 'object',
				'properties'           => [
					'success' => [
						'type'        => 'boolean',
						'description' => __( 'Whether the meta tags data was saved.', 'slim-seo' ),
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

	protected function check_permission( array $input ): bool {
		$this->resolve_term_id( $input );

		if ( ! $this->object_id ) {
			return false;
		}

		return current_user_can( 'edit_term', $this->object_id );
	}

	protected function input_props(): array {
		return [
			'id'       => [
				'type'        => 'integer',
				'description' => __( 'The term ID.', 'slim-seo' ),
			],
			'slug'     => [
				'type'        => 'string',
				'description' => __( 'The term slug.', 'slim-seo' ),
			],
			'name'     => [
				'type'        => 'string',
				'description' => __( 'The term name.', 'slim-seo' ),
			],
			'taxonomy' => [
				'type'        => 'string',
				'description' => __( 'The taxonomy (e.g. category, post_tag). Recommended when using slug or name to avoid ambiguity.', 'slim-seo' ),
			],
		];
	}

	private function get_data( array $input ): array|WP_Error {
		$this->resolve_term_id( $input );

		if ( ! $this->object_id ) {
			return new WP_Error( 'slim_seo_abilities_term_not_found', __( 'The specified term does not exist.', 'slim-seo' ) );
		}

		$data = $this->get_object_data();
		$data = ! empty( $data ) ? $data : Helper::get_default_data( $this->object_type );

		return $this->normalize_data( $data );
	}

	private function update_data( array $input ): array|WP_Error {
		$this->resolve_term_id( $input );

		if ( ! $this->object_id ) {
			return new WP_Error( 'slim_seo_abilities_term_not_found', __( 'The specified term does not exist.', 'slim-seo' ) );
		}

		$this->update_object_data( $input );

		return [ 'success' => true ];
	}

	private function resolve_term_id( array $input ): void {
		if ( ! empty( $input['id'] ) ) {
			$term = get_term( (int) $input['id'] );

			$this->object_id = ( $term && ! is_wp_error( $term ) ) ? $term->term_id : 0;

			return;
		}

		if ( ! empty( $input['slug'] ) ) {
			$args = [ 'slug' => sanitize_title( wp_unslash( $input['slug'] ) ) ];
		} elseif ( ! empty( $input['name'] ) ) {
			$args = [ 'name' => sanitize_text_field( wp_unslash( $input['name'] ) ) ];
		} else {
			return;
		}

		if ( ! empty( $input['taxonomy'] ) ) {
			$args['taxonomy'] = sanitize_key( wp_unslash( $input['taxonomy'] ) );
		}

		$terms = get_terms( array_merge( $args, [
			'hide_empty' => false,
			'number'     => 1,
			'fields'     => 'ids',
		] ) );

		if ( is_wp_error( $terms ) || empty( $terms ) || empty( $terms[0] ) ) {
			return;
		}

		$this->object_id = $terms[0];
	}
}
