<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;
use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Description;

class Term extends Base {
	protected $object_type = 'term';

	protected function ability_config(): array {
		return [
			'slug'               => 'term',
			'get_label'          => __( 'Get term meta tags data', 'slim-seo' ),
			'get_description'    => __( 'Retrieve meta tags for a taxonomy term (title, description, social images, canonical URL and noindex). Provide id, slug, or name.', 'slim-seo' ),
			'update_label'       => __( 'Update term meta tags data', 'slim-seo' ),
			'update_description' => __( 'Update meta tags for a taxonomy term. Provide id, slug, or name. Only provided fields are updated; others remain unchanged.', 'slim-seo' ),
		];
	}

	public function check_permission( array $input ) {
		$error = $this->resolve_input( $input );

		return $error ?: current_user_can( 'edit_term', $this->object_id );
	}

	protected function resolve_input( array $input ) {
		$this->reset_state();

		$error = new WP_Error( 'slim_seo_abilities_term_not_found', __( 'The specified term does not exist.', 'slim-seo' ) );

		if ( ! empty( $input['id'] ) ) {
			$term            = get_term( (int) $input['id'] );
			$this->object_id = $term && ! is_wp_error( $term ) ? $term->term_id : 0;

			return $this->object_id ? null : $error;
		}

		if ( ! empty( $input['slug'] ) ) {
			$args = [ 'slug' => sanitize_title( wp_unslash( $input['slug'] ) ) ];
		} elseif ( ! empty( $input['name'] ) ) {
			$args = [ 'name' => sanitize_text_field( wp_unslash( $input['name'] ) ) ];
		} else {
			return $error;
		}

		if ( ! empty( $input['taxonomy'] ) ) {
			$args['taxonomy'] = sanitize_key( wp_unslash( $input['taxonomy'] ) );
		}

		$terms = get_terms( array_merge( $args, [
			'hide_empty'             => false,
			'number'                 => 1,
			'fields'                 => 'ids',
			'update_term_meta_cache' => false,
		] ) );

		if ( is_wp_error( $terms ) || empty( $terms ) || empty( $terms[0] ) ) {
			return $error;
		}

		$this->object_id = $terms[0];

		return null;
	}

	protected function get_default(): array {
		$default = [
			'title'       => Title::DEFAULTS[ $this->object_type ],
			'description' => Description::DEFAULTS[ $this->object_type ],
		];
		$term    = get_term( $this->object_id );

		if ( ! $term || is_wp_error( $term ) ) {
			return $default;
		}

		$settings = $this->get_settings();

		return array_merge( $default, $settings[ $term->taxonomy ] ?? [] );
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
}
