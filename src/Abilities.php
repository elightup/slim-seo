<?php
namespace SlimSEO;

use WP_Error;
use eLightUp\SlimSEO\Common\Helpers\Data as CommonHelpersData;
use SlimSEO\Helpers\Data;
use SlimSEO\MetaTags\Helper;
use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Description;

class Abilities {
	public function setup(): void {
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
				return $this->check_permission( $input, 'post' );
			},
			'execute_callback'    => function ( array $input ): array {
				return $this->get_data( $input, 'post' );
			},
		] );

		wp_register_ability( 'slim-seo/update-post-seo', [
			'category'            => 'slim-seo',
			'label'               => __( 'Update post SEO data', 'slim-seo' ),
			'description'         => __( 'Update SEO meta tags for a post. Provide id, slug, or title. Only provided fields are updated; others remain unchanged.', 'slim-seo' ),
			'input_schema'        => $this->get_update_schema( 'post' ),
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
				return $this->check_permission( $input, 'post' );
			},
			'execute_callback'    => function ( array $input ): array {
				return $this->update_data( $input, 'post' );
			},
		] );

		wp_register_ability( 'slim-seo/get-term-seo', [
			'category'            => 'slim-seo',
			'label'               => __( 'Get term SEO data', 'slim-seo' ),
			'description'         => __( 'Retrieve SEO meta tags for a taxonomy term (title, description, social images, canonical URL, noindex). Provide id, slug, or name.', 'slim-seo' ),
			'input_schema'        => [
				'type'                 => 'object',
				'properties'           => [
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
				],
				'additionalProperties' => false,
			],
			'output_schema'       => $schema,
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
				return $this->check_permission( $input, 'term' );
			},
			'execute_callback'    => function ( array $input ): array {
				return $this->get_data( $input, 'term' );
			},
		] );

		wp_register_ability( 'slim-seo/update-term-seo', [
			'category'            => 'slim-seo',
			'label'               => __( 'Update term SEO data', 'slim-seo' ),
			'description'         => __( 'Update SEO meta tags for a taxonomy term. Provide id, slug, or name. Only provided fields are updated; others remain unchanged.', 'slim-seo' ),
			'input_schema'        => $this->get_update_schema( 'term' ),
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
				return $this->check_permission( $input, 'term' );
			},
			'execute_callback'    => function ( array $input ): array {
				return $this->update_data( $input, 'term' );
			},
		] );

		unset( $schema['properties']['canonical'] );

		wp_register_ability( 'slim-seo/get-settings', [
			'category'            => 'slim-seo',
			'label'               => __( 'Get SEO settings', 'slim-seo' ),
			'description'         => __( 'Retrieve global SEO settings (homepage, post types, taxonomies, archives). Returns title, description, and noindex for each context.', 'slim-seo' ),
			'input_schema'        => [
				'type'                 => 'object',
				'properties'           => [
					'context' => [
						'type'        => 'string',
						'description' => __( 'The settings context to retrieve. Use "home" for homepage, a post type slug (e.g. "post", "page") for post types, a post type slug suffixed with "_archive" (e.g. "movie_archive") for archives, or a taxonomy slug (e.g. "category", "post_tag") for taxonomies.', 'slim-seo' ),
					],
				],
				'additionalProperties' => false,
			],
			'output_schema'       => $schema,
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
				return $this->check_permission( $input, 'settings' );
			},
			'execute_callback'    => function ( array $input ): array {
				return $this->get_data( $input, 'settings' );
			},
		] );

		wp_register_ability( 'slim-seo/update-settings', [
			'category'            => 'slim-seo',
			'label'               => __( 'Update SEO settings', 'slim-seo' ),
			'description'         => __( 'Update global SEO settings. Only provided fields are updated.', 'slim-seo' ),
			'input_schema'        => $this->get_update_schema( 'settings' ),
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
				return $this->check_permission( $input, 'settings' );
			},
			'execute_callback'    => function ( array $input ): array {
				return $this->update_data( $input, 'settings' );
			},
		] );
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

	private function get_data( array $input, string $object_type ): array|WP_Error {
		if ( 'settings' === $object_type ) {
			$context      = sanitize_key( wp_unslash( $input['context'] ?? '' ) );
			$context_type = $this->get_context_type( $context );

			if ( empty( $context_type ) ) {
				return new WP_Error(
					'slim_seo_abilities_invalid_context',
					__( 'Invalid settings context.', 'slim-seo' )
				);
			}

			$settings = $this->get_object_data( 'settings' );
			$data     = $settings[ $context ] ?? [];
			$data     = ! empty( $data ) ? $data : $this->get_default_data( $context_type );

			return $this->normalize_data( $data, 0, $object_type );
		}

		$id = 'post' === $object_type ? $this->resolve_post_id( $input ) : $this->resolve_term_id( $input );

		if ( ! $id ) {
			return new WP_Error(
				'slim_seo_abilities_object_not_found',
				/* translators: %s: object type (post or term) */
				sprintf( __( 'The specified %s does not exist.', 'slim-seo' ), $object_type )
			);
		}

		$data = $this->get_object_data( $object_type, $id );
		$data = ! empty( $data ) ? $data : $this->get_default_data( $object_type );

		return $this->normalize_data( $data, $id, $object_type );
	}

	private function update_data( array $input, string $object_type ): array|WP_Error {
		if ( 'settings' === $object_type ) {
			$context      = sanitize_key( wp_unslash( $input['context'] ?? '' ) );
			$context_type = $this->get_context_type( $context );

			if ( empty( $context_type ) ) {
				return new WP_Error(
					'slim_seo_abilities_invalid_context',
					__( 'Invalid settings context.', 'slim-seo' )
				);
			}

			$settings = $this->get_object_data( 'settings' );
			$data     = $this->sanitize_data( $settings[ $context ] ?? [], $input, 'settings' );

			if ( empty( $data ) ) {
				unset( $settings[ $context ] );
			} else {
				$settings[ $context ] = $data;
			}

			$this->update_object_data( 'settings', 0, $settings );

			return [ 'success' => true ];
		}

		$id = 'post' === $object_type ? $this->resolve_post_id( $input ) : $this->resolve_term_id( $input );

		if ( ! $id ) {
			return new WP_Error(
				'slim_seo_abilities_object_not_found',
				/* translators: %s: object type (post or term) */
				sprintf( __( 'The specified %s does not exist.', 'slim-seo' ), $object_type )
			);
		}

		$data = $this->get_object_data( $object_type, $id );
		$data = $this->sanitize_data( $data, $input );

		$this->update_object_data( $object_type, $id, $data );

		return [ 'success' => true ];
	}

	private function check_permission( array $input, string $object_type ): bool {
		if ( 'settings' === $object_type ) {
			return current_user_can( 'manage_options' );
		}

		$object_id = 'post' === $object_type ? $this->resolve_post_id( $input ) : $this->resolve_term_id( $input );

		if ( ! $object_id ) {
			return false;
		}

		$cap = 'post' === $object_type ? 'edit_post' : 'edit_term';

		return current_user_can( $cap, $object_id );
	}

	private function resolve_post_id( array $input ): int {
		if ( ! empty( $input['id'] ) ) {
			$post = get_post( (int) $input['id'] );

			return $post ? $post->ID : 0;
		}

		if ( ! empty( $input['slug'] ) ) {
			$args = [ 'name' => sanitize_title( wp_unslash( $input['slug'] ) ) ];
		} elseif ( ! empty( $input['post_title'] ) ) {
			$args = [ 'title' => sanitize_text_field( wp_unslash( $input['post_title'] ) ) ];
		} else {
			return 0;
		}

		$posts = get_posts( array_merge( $args, [
			'post_type'      => Data::get_meta_box_post_types(),
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		] ) );

		if ( is_wp_error( $posts ) || empty( $posts ) || empty( $posts[0] ) ) {
			return 0;
		}

		return $posts[0];
	}

	private function resolve_term_id( array $input ): int {
		if ( ! empty( $input['id'] ) ) {
			$term = get_term( (int) $input['id'] );

			return ( $term && ! is_wp_error( $term ) ) ? $term->term_id : 0;
		}

		if ( ! empty( $input['slug'] ) ) {
			$args = [ 'slug' => sanitize_title( wp_unslash( $input['slug'] ) ) ];
		} elseif ( ! empty( $input['name'] ) ) {
			$args = [ 'name' => sanitize_text_field( wp_unslash( $input['name'] ) ) ];
		} else {
			return 0;
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
			return 0;
		}

		return $terms[0];
	}

	private function get_schema( bool $detailed = true ): array {
		$fields = [
			'title'          => __( 'Meta title.', 'slim-seo' ),
			'description'    => __( 'Meta description.', 'slim-seo' ),
			'facebook_image' => __( 'Open Graph image URL.', 'slim-seo' ),
			'x_image'        => __( 'X image URL.', 'slim-seo' ),
			'canonical'      => __( 'Canonical URL.', 'slim-seo' ),
		];

		$properties = [];

		foreach ( $fields as $key => $description ) {
			$property = [
				'type'        => $detailed ? 'object' : 'string',
				'description' => $description,
			];

			if ( $detailed ) {
				$property['properties']           = [
					'raw'      => [
						'type'        => 'string',
						'description' => __( 'Raw value with dynamic variables.', 'slim-seo' ),
					],
					'rendered' => [
						'type'        => 'string',
						'description' => __( 'Rendered value with variables replaced.', 'slim-seo' ),
					],
				];
				$property['additionalProperties'] = false;
			}

			$properties[ $key ] = $property;
		}

		$properties['noindex'] = [
			'type'        => 'boolean',
			'description' => __( 'Whether the post/page should be excluded from search engines.', 'slim-seo' ),
		];

		return $detailed
			? [
				'type'                 => 'object',
				'properties'           => $properties,
				'additionalProperties' => false,
			]
			: $properties;
	}

	private function get_update_schema( string $object_type = 'post' ): array {
		$properties = $this->get_schema( false );

		switch ( $object_type ) {
			case 'term':
				$properties = array_merge( $properties, [
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
				] );
				break;
			case 'settings':
				unset( $properties['canonical'] );

				$properties = array_merge( $properties, [
					'context' => [
						'type'        => 'string',
						'description' => __( 'The settings context to update. Use "home" for homepage, a post type slug (e.g. "post", "page") for post types, a post type slug suffixed with "_archive" (e.g. "movie_archive") for archives, or a taxonomy slug (e.g. "category", "post_tag") for taxonomies.', 'slim-seo' ),
					],
				] );
				break;
			default:
				$properties = array_merge( $properties, [
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
				] );
				break;
		}

		return [
			'type'                 => 'object',
			'properties'           => $properties,
			'additionalProperties' => false,
		];
	}

	private function get_object_data( string $object_type, int $object_id = 0 ): array {
		if ( 'settings' === $object_type ) {
			return get_option( 'slim_seo', [] );
		}

		return get_metadata( $object_type, $object_id, 'slim_seo', true ) ?: [];
	}

	private function update_object_data( string $object_type, int $object_id = 0, array $data = [] ): void {
		if ( 'settings' === $object_type ) {
			update_option( 'slim_seo', $data );

			return;
		}

		update_metadata( $object_type, $object_id, 'slim_seo', $data );
	}

	private function get_default_data( string $object_type ): array {
		return [
			'title'       => Title::DEFAULTS[ $object_type ] ?? '',
			'description' => Description::DEFAULTS[ $object_type ] ?? '',
		];
	}

	private function normalize_data( array $data, int $object_id = 0, string $object_type = 'post' ): array {
		$data = [
			'title'          => $this->render_field( $data['title'] ?? '', $object_id, $object_type ),
			'description'    => $this->render_field( $data['description'] ?? '', $object_id, $object_type ),
			'facebook_image' => $this->render_field( $data['facebook_image'] ?? '', $object_id, $object_type ),
			'x_image'        => $this->render_field( $data['twitter_image'] ?? '', $object_id, $object_type ),
			'noindex'        => (int) ( $data['noindex'] ?? 0 ),
		];

		if ( 'settings' !== $object_type ) {
			$data['canonical'] = $this->render_field( $data['canonical'] ?? '', $object_id, $object_type );
		}

		return $data;
	}

	private function render_field( string $value, int $object_id, string $object_type ): array {
		$post_id = 'post' === $object_type ? $object_id : 0;
		$term_id = 'term' === $object_type ? $object_id : 0;

		return [
			'raw'      => $value,
			'rendered' => Helper::render( $value, $post_id, $term_id ),
		];
	}

	private function sanitize_data( array $data, array $input, string $object_type = 'post' ): array {
		$fields = [ 'title', 'description', 'facebook_image' ];

		if ( 'settings' !== $object_type ) {
			$fields[] = 'canonical';
		}

		foreach ( $fields as $field ) {
			if ( ! isset( $input[ $field ] ) ) {
				continue;
			}

			$data[ $field ] = sanitize_text_field( $input[ $field ] );
		}

		if ( isset( $input['x_image'] ) ) {
			$data['twitter_image'] = sanitize_text_field( $input['x_image'] );
		}

		if ( isset( $input['noindex'] ) ) {
			$data['noindex'] = $input['noindex'] ? 1 : 0;
		}

		return array_filter( $data );
	}
}
