<?php
namespace SlimSEO\Integrations\ACF;

class ACF {
	private $variables;

	public function is_active(): bool {
		return class_exists( 'ACF' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_variables', [ $this, 'add_variables' ] );
		add_filter( 'slim_seo_data', [ $this, 'add_data' ], 10, 3 );
	}

	public function add_variables( array $variables ): array {
		$this->variables = $variables;

		$field_groups = acf_get_field_groups();
		array_walk( $field_groups, [ $this, 'add_group' ] );

		return $this->variables;
	}

	public function add_data( array $data, int $post_id, int $term_id ): array {
		$field_objects = [];
		if ( $post_id ) {
			// Specify a post ID.
			$field_objects = $this->get_post_field_objects( $post_id );
		} elseif ( $term_id ) {
			// Specify a term ID.
			$field_objects = $this->get_term_field_objects( $term_id );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			// On a term archive page.
			$field_objects = $this->get_term_field_objects( get_queried_object_id() );
		} else {
			// Fallback to get post field objects from the current post.
			$post_id = is_singular() ? get_queried_object_id() : (int) get_the_ID();
			$field_objects = $this->get_post_field_objects( $post_id );
		}

		// Option fields.
		if ( function_exists( 'acf_add_options_page' ) ) {
			$option_field_objects = get_field_objects( 'option' );

			if ( ! empty( $option_field_objects ) ) {
				$field_objects = array_merge( $option_field_objects, $field_objects );
			}
		}

		if ( ! empty( $field_objects ) ) {
			$data['acf'] = new Renderer( $field_objects );
		}

		return $data;
	}

	private function get_post_field_objects( int $post_id ): array {
		if ( ! $post_id ) {
			return [];
		}

		$field_objects = get_field_objects( $post_id ) ?: [];

		// Post author fields.
		$post = get_post( $post_id );
		if ( ! $post ) {
			return $field_objects;
		}

		$author_field_objects = get_field_objects( 'user_' . $post->post_author ) ?: [];

		return array_merge( $author_field_objects, $field_objects );
	}

	private function get_term_field_objects( int $term_id ): array {
		if ( ! $term_id ) {
			return [];
		}

		return get_field_objects( "term_$term_id" ) ?: [];
	}

	private function add_group( array $field_group ): void {
		$this->variables[] = [
			// Translators: %s - field group title.
			'label'   => sprintf( __( '[ACF] %s', 'slim-seo' ), $field_group['title'] ),
			'options' => $this->add_fields( acf_get_fields( $field_group['key'] ), 'acf' ),
		];
	}

	private function add_fields( array $fields, string $base_id = '', string $indent = '' ): array {
		$options    = [];
		$fields     = array_filter( $fields, [ $this, 'has_value' ] );
		$sub_indent = $indent . str_repeat( '&nbsp;', 5 );

		foreach ( $fields as $field ) {
			$field_name = $field['name'];
			$id         = "$base_id.{$field_name}";
			$label      = "{$indent}{$field['label']}";

			if ( 'google_map' === $field['type'] ) {
				// Translators: %s - Field name.
				$options[ $id . '.address' ] = sprintf( __( '%s (address)', 'slim-seo' ), $label );
				// Translators: %s - Field name.
				$options[ $id . '.lat' ] = sprintf( __( '%s (latitude)', 'slim-seo' ), $label );
				// Translators: %s - Field name.
				$options[ $id . '.lng' ] = sprintf( __( '%s (longitude)', 'slim-seo' ), $label );
				// Translators: %s - Field name.
				$options[ $id . '.name' ] = sprintf( __( '%s (name)', 'slim-seo' ), $label );
				// Translators: %s - Field name.
				$options[ $id . '.city' ] = sprintf( __( '%s (city)', 'slim-seo' ), $label );
				// Translators: %s - Field name.
				$options[ $id . '.state' ] = sprintf( __( '%s (state)', 'slim-seo' ), $label );
				// Translators: %s - Field name.
				$options[ $id . '.country' ] = sprintf( __( '%s (country)', 'slim-seo' ), $label );
				// Translators: %s - Field name.
				$options[ $id . '.country_short' ] = sprintf( __( '%s (country short name)', 'slim-seo' ), $label );
			} else {
				$options[ $id ] = $label;
			}

			if ( ! empty( $field['sub_fields'] ) ) {
				$options = array_merge( $options, $this->add_fields( $field['sub_fields'], $id, $sub_indent ) );
			}

			if ( ! empty( $field['layouts'] ) ) {
				$options = array_merge( $options, $this->add_fields( $field['layouts'], $id, $sub_indent ) );
			}
		}
		return $options;
	}

	private function has_value( array $field ): bool {
		return ! in_array( $field['type'] ?? '', [ 'message', 'accordion', 'tab' ], true );
	}
}
