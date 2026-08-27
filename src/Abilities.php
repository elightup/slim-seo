<?php
namespace SlimSEO;

use eLightUp\SlimSEO\Common\Helpers\Data as CommonHelpersData;

class Abilities {
	public function __construct() {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		add_action( 'wp_abilities_api_categories_init', [ $this, 'register_category' ] );
		add_action( 'wp_abilities_api_init', [ $this, 'register_abilities' ] );
	}

	public function register_category(): void {
		if ( wp_has_ability_category( 'slim-seo' ) ) {
			return;
		}

		wp_register_ability_category( 'slim-seo', [
			'label'       => __( 'Slim SEO', 'slim-seo' ),
			'description' => __( 'Abilities for Slim SEO data (meta tags, titles, descriptions, social images, etc.).', 'slim-seo' ),
		] );
	}

	public function register_abilities(): void {
		$this->register_post_abilities();
		$this->register_term_abilities();
	}

	public function get_post( array $input ): array {
		$id = $this->resolve_post_id( $input );

		if ( ! $id ) {
			return [];
		}

		return $this->normalize_data( $this->get_data( 'post', $id ) );
	}

	public function update_post( array $input ): array {
		$id = $this->resolve_post_id( $input );

		if ( ! $id ) {
			return [ 'success' => false ];
		}

		$data = $this->get_data( 'post', $id );
		$data = $this->sanitize_data( $data, $input );

		$this->update_data( 'post', $id, $data );

		return [ 'success' => true ];
	}

	public function get_term( array $input ): array {
		$id = $this->resolve_term_id( $input );

		if ( ! $id ) {
			return [];
		}

		return $this->normalize_data( $this->get_data( 'term', $id ) );
	}

	public function update_term( array $input ): array {
		$id = $this->resolve_term_id( $input );

		if ( ! $id ) {
			return [ 'success' => false ];
		}

		$data = $this->get_data( 'term', $id );
		$data = $this->sanitize_data( $data, $input );

		$this->update_data( 'term', $id, $data );

		return [ 'success' => true ];
	}

	private function check_permission( array $input, string $object_type, string $action ): bool {
		$object_id = 'post' === $object_type ? $this->resolve_post_id( $input ) : $this->resolve_term_id( $input );

		if ( ! $object_id ) {
			return false;
		}

		$cap = $this->map_capability( $object_type, $action );

		return current_user_can( $cap, $object_id );
	}

	private function map_capability( string $object_type, string $action ): string {
		$is_read = 'get' === $action;

		switch ( $object_type ) {
			case 'term':
				return $is_read ? 'assign_term' : 'edit_term';
			case 'post':
			default:
				return $is_read ? 'read_post' : 'edit_post';
		}
	}

	private function register_post_abilities(): void {
		$schema = $this->get_schema();

		wp_register_ability( 'slim-seo/get-post-seo', [
			'category'            => 'slim-seo',
			'label'               => __( 'Get post SEO data', 'slim-seo' ),
			'description'         => __( 'Retrieve SEO meta tags for a post (title, description, social images, canonical URL, noindex). Provide id, slug, or title.', 'slim-seo' ),
			'input_schema'        => [
				'type'                 => 'object',
				'properties'           => [
					'id'         => [
						'type'        => 'integer',
						'description' => __( 'The post ID.', 'slim-seo' ),
					],
					'slug'       => [
						'type'        => 'string',
						'description' => __( 'The post slug.', 'slim-seo' ),
					],
					'post_title' => [
						'type'        => 'string',
						'description' => __( 'The post title.', 'slim-seo' ),
					],
				],
				'additionalProperties' => false,
			],
			'output_schema'       => $schema,
			'meta'                => [
				'annotations' => [
					'readonly'      => true,
					'destructive'   => false,
					'openWorldHint' => false,
				],
				'mcp'         => [
					'public' => true,
					'type'   => 'tool',
				],
			],
			'permission_callback' => function ( $input ) {
				return $this->check_permission( $input, 'post', 'get' );
			},
			'execute_callback'    => [ $this, 'get_post' ],
		] );

		wp_register_ability( 'slim-seo/update-post-seo', [
			'category'            => 'slim-seo',
			'label'               => __( 'Update post SEO data', 'slim-seo' ),
			'description'         => __( 'Update SEO meta tags for a post. Provide id, slug, or title. Only provided fields are updated; others remain unchanged.', 'slim-seo' ),
			'input_schema'        => $this->get_post_update_schema(),
			'output_schema'       => [
				'type'                 => 'object',
				'properties'           => [
					'success' => [
						'type'        => 'boolean',
						'description' => __( 'Whether the SEO data was saved.', 'slim-seo' ),
					],
				],
				'additionalProperties' => false,
			],
			'meta'                => [
				'annotations' => [
					'readonly'    => false,
					'destructive' => false,
					'idempotent'  => true,
				],
				'mcp'         => [
					'public' => true,
					'type'   => 'tool',
				],
			],
			'permission_callback' => function ( $input ) {
				return $this->check_permission( $input, 'post', 'update' );
			},
			'execute_callback'    => [ $this, 'update_post' ],
		] );
	}

	private function register_term_abilities(): void {
		$schema = $this->get_schema();

		wp_register_ability( 'slim-seo/get-term-seo', [
			'category'            => 'slim-seo',
			'label'               => __( 'Get term SEO data', 'slim-seo' ),
			'description'         => __( 'Retrieve SEO meta tags for a taxonomy term (title, description, social images, canonical URL, noindex). Provide id, slug, or name.', 'slim-seo' ),
			'input_schema'        => [
				'type'                 => 'object',
				'properties'           => [
					'id'   => [
						'type'        => 'integer',
						'description' => __( 'The term ID.', 'slim-seo' ),
					],
					'slug' => [
						'type'        => 'string',
						'description' => __( 'The term slug.', 'slim-seo' ),
					],
					'name' => [
						'type'        => 'string',
						'description' => __( 'The term name.', 'slim-seo' ),
					],
				],
				'additionalProperties' => false,
			],
			'output_schema'       => $schema,
			'meta'                => [
				'annotations' => [
					'readonly'      => true,
					'destructive'   => false,
					'openWorldHint' => false,
				],
				'mcp'         => [
					'public' => true,
					'type'   => 'tool',
				],
			],
			'permission_callback' => function ( $input ) {
				return $this->check_permission( $input, 'term', 'get' );
			},
			'execute_callback'    => [ $this, 'get_term' ],
		] );

		wp_register_ability( 'slim-seo/update-term-seo', [
			'category'            => 'slim-seo',
			'label'               => __( 'Update term SEO data', 'slim-seo' ),
			'description'         => __( 'Update SEO meta tags for a taxonomy term. Provide id, slug, or name. Only provided fields are updated; others remain unchanged.', 'slim-seo' ),
			'input_schema'        => $this->get_term_update_schema(),
			'output_schema'       => [
				'type'                 => 'object',
				'properties'           => [
					'success' => [
						'type'        => 'boolean',
						'description' => __( 'Whether the SEO data was saved.', 'slim-seo' ),
					],
				],
				'additionalProperties' => false,
			],
			'meta'                => [
				'annotations' => [
					'readonly'    => false,
					'destructive' => false,
					'idempotent'  => true,
				],
				'mcp'         => [
					'public' => true,
					'type'   => 'tool',
				],
			],
			'permission_callback' => function ( $input ) {
				return $this->check_permission( $input, 'term', 'update' );
			},
			'execute_callback'    => [ $this, 'update_term' ],
		] );
	}

	private function get_schema(): array {
		return [
			'type'                 => 'object',
			'properties'           => [
				'title'          => [
					'type'        => 'string',
					'description' => __( 'Meta title.', 'slim-seo' ),
				],
				'description'    => [
					'type'        => 'string',
					'description' => __( 'Meta description.', 'slim-seo' ),
				],
				'facebook_image' => [
					'type'        => 'string',
					'description' => __( 'Open Graph image URL.', 'slim-seo' ),
				],
				'twitter_image'  => [
					'type'        => 'string',
					'description' => __( 'Twitter card image URL.', 'slim-seo' ),
				],
				'canonical'      => [
					'type'        => 'string',
					'description' => __( 'Canonical URL.', 'slim-seo' ),
				],
				'noindex'        => [
					'type'        => 'boolean',
					'description' => __( 'noindex.', 'slim-seo' ),
				],
			],
			'additionalProperties' => false,
		];
	}

	private function get_post_update_schema(): array {
		$properties               = $this->get_schema()['properties'];
		$properties['id']         = [
			'type'        => 'integer',
			'description' => __( 'The post ID.', 'slim-seo' ),
		];
		$properties['slug']       = [
			'type'        => 'string',
			'description' => __( 'The post slug.', 'slim-seo' ),
		];
		$properties['post_title'] = [
			'type'        => 'string',
			'description' => __( 'The post title.', 'slim-seo' ),
		];

		return [
			'type'                 => 'object',
			'properties'           => $properties,
			'additionalProperties' => false,
		];
	}

	private function get_term_update_schema(): array {
		$properties         = $this->get_schema()['properties'];
		$properties['id']   = [
			'type'        => 'integer',
			'description' => __( 'The term ID.', 'slim-seo' ),
		];
		$properties['slug'] = [
			'type'        => 'string',
			'description' => __( 'The term slug.', 'slim-seo' ),
		];
		$properties['name'] = [
			'type'        => 'string',
			'description' => __( 'The term name.', 'slim-seo' ),
		];

		return [
			'type'                 => 'object',
			'properties'           => $properties,
			'additionalProperties' => false,
		];
	}

	private function resolve_post_id( array $input ): int {
		if ( ! empty( $input['id'] ) ) {
			return (int) $input['id'];
		} elseif ( ! empty( $input['slug'] ) ) {
			$field = 'name';
			$value = sanitize_title( $input['slug'] );
		} elseif ( ! empty( $input['post_title'] ) ) {
			$field = 'title';
			$value = sanitize_text_field( $input['post_title'] );
		} else {
			return 0;
		}

		$posts = get_posts( [
			'post_type'      => array_keys( CommonHelpersData::get_post_types() ),
			$field           => $value,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		] );

		if ( is_wp_error( $posts ) || empty( $posts ) || empty( $posts[0] ) ) {
			return 0;
		}

		return $posts[0];
	}

	private function resolve_term_id( array $input ): int {
		if ( ! empty( $input['id'] ) ) {
			return (int) $input['id'];
		} elseif ( ! empty( $input['slug'] ) ) {
			$field = 'slug';
			$value = sanitize_title( $input['slug'] );
		} elseif ( ! empty( $input['name'] ) ) {
			$field = 'name';
			$value = sanitize_text_field( $input['name'] );
		} else {
			return 0;
		}

		$terms = get_terms( [
			$field       => $value,
			'hide_empty' => false,
			'number'     => 1,
			'fields'     => 'ids',
		] );

		if ( is_wp_error( $terms ) || empty( $terms ) || empty( $terms[0] ) ) {
			return 0;
		}

		return $terms[0];
	}

	private function get_data( string $object_type, int $object_id ): array {
		return get_metadata( $object_type, $object_id, 'slim_seo', true ) ?: [];
	}

	private function update_data( string $object_type, int $object_id, array $data ): void {
		update_metadata( $object_type, $object_id, 'slim_seo', $data );
	}

	private function normalize_data( array $data ): array {
		return [
			'title'          => $data['title'] ?? '',
			'description'    => $data['description'] ?? '',
			'facebook_image' => $data['facebook_image'] ?? '',
			'twitter_image'  => $data['twitter_image'] ?? '',
			'canonical'      => $data['canonical'] ?? '',
			'noindex'        => $data['noindex'] ?? false,
		];
	}

	private function sanitize_data( array $data, array $input ): array {
		$fields = [ 'title', 'description', 'facebook_image', 'twitter_image', 'canonical' ];

		foreach ( $fields as $field ) {
			if ( ! isset( $input[ $field ] ) ) {
				continue;
			}

			$data[ $field ] = sanitize_text_field( $input[ $field ] );
		}

		if ( isset( $input['noindex'] ) ) {
			$data['noindex'] = $input['noindex'] ? 1 : 0;
		}

		return $data;
	}
}
