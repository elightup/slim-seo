<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;
use SlimSEO\Helpers\Data;

class Post extends Base {
	protected $object_type = 'post';
	protected $object_id   = 0;

	public function register_abilities(): void {
		wp_register_ability( 'slim-seo/get-post-meta-tags', [
			'category'            => 'slim-seo',
			'label'               => __( 'Get post meta tags data', 'slim-seo' ),
			'description'         => __( 'Retrieve meta tags for a post (title, description, social images, canonical URL, noindex). Provide id, slug, or title.', 'slim-seo' ),
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

		wp_register_ability( 'slim-seo/update-post-meta-tags', [
			'category'            => 'slim-seo',
			'label'               => __( 'Update post meta tags data', 'slim-seo' ),
			'description'         => __( 'Update meta tags for a post. Provide id, slug, or title. Only provided fields are updated; others remain unchanged.', 'slim-seo' ),
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
		$this->resolve_post_id( $input );

		if ( ! $this->object_id ) {
			return false;
		}

		return current_user_can( 'edit_post', $this->object_id );
	}

	protected function input_props(): array {
		return [
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
		];
	}

	private function get_data( array $input ): array|WP_Error {
		$this->resolve_post_id( $input );

		if ( ! $this->object_id ) {
			return new WP_Error( 'slim_seo_abilities_post_not_found', __( 'The specified post does not exist.', 'slim-seo' ) );
		}

		$data = $this->get_object_data();
		$data = ! empty( $data ) ? $data : Helper::get_default_data( $this->object_type );

		return $this->normalize_data( $data );
	}

	private function update_data( array $input ): array|WP_Error {
		$this->resolve_post_id( $input );

		if ( ! $this->object_id ) {
			return new WP_Error( 'slim_seo_abilities_post_not_found', __( 'The specified post does not exist.', 'slim-seo' ) );
		}

		$this->update_object_data( $input );

		return [ 'success' => true ];
	}

	private function resolve_post_id( array $input ): void {
		if ( $this->object_id ) {
			return;
		}

		if ( ! empty( $input['id'] ) ) {
			$post = get_post( (int) $input['id'] );

			$this->object_id = $post ? $post->ID : 0;

			return;
		}

		if ( ! empty( $input['slug'] ) ) {
			$args = [ 'name' => sanitize_title( wp_unslash( $input['slug'] ) ) ];
		} elseif ( ! empty( $input['post_title'] ) ) {
			$args = [ 'title' => sanitize_text_field( wp_unslash( $input['post_title'] ) ) ];
		} else {
			return;
		}

		$posts = get_posts( array_merge( $args, [
			'post_type'      => Data::get_meta_box_post_types(),
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		] ) );

		if ( is_wp_error( $posts ) || empty( $posts ) || empty( $posts[0] ) ) {
			return;
		}

		$this->object_id = $posts[0];
	}
}
