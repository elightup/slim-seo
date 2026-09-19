<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;
use SlimSEO\Helpers\Data;

class Post extends Base {
	protected function ability_config(): array {
		return [
			'slug'               => 'post',
			'get_label'          => __( 'Get post meta tags data', 'slim-seo' ),
			'get_description'    => __( 'Retrieve meta tags for a post (title, description, social images, canonical URL and noindex). Provide id, slug, or title.', 'slim-seo' ),
			'update_label'       => __( 'Update post meta tags data', 'slim-seo' ),
			'update_description' => __( 'Update meta tags for a post. Provide id, slug, or title. Only provided fields are updated; others remain unchanged.', 'slim-seo' ),
		];
	}

	protected function check_permission( array $input ) {
		$error = $this->resolve_post_id( $input );

		if ( $error ) {
			return $error;
		}

		return current_user_can( 'edit_post', $this->object_id );
	}

	private function resolve_post_id( array $input ) {
		if ( $this->object_id ) {
			return null;
		}

		$error = new WP_Error( 'slim_seo_abilities_post_not_found', __( 'The specified post does not exist.', 'slim-seo' ) );

		if ( ! empty( $input['id'] ) ) {
			$post = get_post( (int) $input['id'] );

			$this->object_id = $post ? $post->ID : 0;

			return $this->object_id ? null : $error;
		}

		if ( ! empty( $input['slug'] ) ) {
			$args = [ 'name' => sanitize_title( wp_unslash( $input['slug'] ) ) ];
		} elseif ( ! empty( $input['post_title'] ) ) {
			$args = [ 'title' => sanitize_text_field( wp_unslash( $input['post_title'] ) ) ];
		} else {
			return $error;
		}

		$posts = get_posts( array_merge( $args, [
			'post_type'      => Data::get_meta_box_post_types(),
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		] ) );

		if ( is_wp_error( $posts ) || empty( $posts ) || empty( $posts[0] ) ) {
			return $error;
		}

		$this->object_id = $posts[0];

		return null;
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

	protected function get_data( array $input ) {
		$error = $this->resolve_post_id( $input );

		if ( $error ) {
			return $error;
		}

		$data = $this->get_object_data();
		$data = ! empty( $data ) ? $data : $this->default_data();

		return $this->normalize_data( $data );
	}

	protected function update_data( array $input ) {
		$error = $this->resolve_post_id( $input );

		if ( $error ) {
			return $error;
		}

		$this->update_object_data( $input );

		return [ 'success' => true ];
	}
}
