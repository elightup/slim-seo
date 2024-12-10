<?php
namespace SlimSEO\Integrations\MetaBox;

class MetaBox {
	private $variables;

	public function is_active(): bool {
		return defined( 'RWMB_VER' ) && function_exists( 'rwmb_get_registry' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
		add_filter( 'slim_seo_skipped_blocks', [ $this, 'skip_blocks' ] );
		add_filter( 'slim_seo_variables', [ $this, 'add_variables' ] );
		add_filter( 'slim_seo_data', [ $this, 'add_data' ], 10, 3 );
	}

	public function skip_shortcodes( array $shortcodes ): array {
		return array_merge( $shortcodes, [
			'rwmb_meta',                // Meta Box.
			'mb_frontend_form',         // MB Frontend Submission.
			'mb_frontend_dashboard',
			'mb_user_profile_register', // MB User Profile.
			'mb_user_profile_login',
			'mb_user_profile_info',
			'mb_relationships',         // MB Relationships.
			'mbfp-button',              // MB Favorite Posts.
		] );
	}

	public function skip_blocks( array $blocks ): array {
		return array_merge( $blocks, [
			'meta-box/submission-form',   // MB Frontend Submission.
			'meta-box/user-dashboard',
			'meta-box/login-form',        // MB User Profile.
			'meta-box/profile-form',
			'meta-box/registration-form',
		] );
	}

	public function add_variables( array $variables ): array {
		$this->variables = $variables;
		$meta_boxes      = $this->get_meta_boxes();
		array_walk( $meta_boxes, [ $this, 'add_group' ] );

		return $this->variables;
	}

	public function add_data( array $data, int $post_id, int $term_id ): array {
		$meta_boxes = $this->get_meta_boxes();

		$mb = [];
		foreach ( $meta_boxes as $meta_box ) {
			$key        = Id::normalize( $meta_box->id );
			$mb[ $key ] = new Renderer( $meta_box, $post_id, $term_id );
		}

		$data['mb'] = $mb;

		return $data;
	}

	private function get_meta_boxes(): array {
		$meta_boxes = rwmb_get_registry( 'meta_box' )->all();
		$meta_boxes = array_filter( $meta_boxes, [ $this, 'remove_built_in' ] );

		return $meta_boxes;
	}

	private function remove_built_in( $meta_box ): bool {
		$built_in = [
			// MB Favorite Posts.
			'mbfp-preview-section',
			'mbfp_posts',

			// MB Views.
			'mbv-template-editor',
			'mbv-settings',
			'mbv-shortcode',

			// MB User Profile.
			'rwmb-user-register',
			'rwmb-user-login',
			'rwmb-user-lost-password',
			'rwmb-user-reset-password',
			'rwmb-user-info',
		];

		$is_relationship = preg_match( '/(_relationships_from|_relationships_to)$/', $meta_box->id );
		return ! in_array( $meta_box->id, $built_in, true ) && ! $is_relationship;
	}

	private function add_group( $meta_box ): void {
		$key               = Id::normalize( $meta_box->id );
		$this->variables[] = [
			'label'   => "[Meta Box] {$meta_box->title}",
			'options' => $this->add_fields( $meta_box->fields, "mb.$key" ),
		];
	}

	private function add_fields( array $fields, string $base_id = '', string $indent = '' ): array {
		$options    = [];
		$fields     = array_filter( $fields, [ $this, 'has_value' ] );
		$sub_indent = $indent . str_repeat( '&nbsp;', 5 );

		foreach ( $fields as $field ) {
			$key   = Id::normalize( $field['id'] );
			$id    = "$base_id.$key";
			$label = "{$indent}{$field['name']}";

			if ( in_array( $field['type'], [ 'map', 'osm' ], true ) ) {
				// Translators: %s - Field name.
				$options[ $id . '.latitude' ] = sprintf( __( '%s (latitude)', 'slim-seo' ), $label );
				// Translators: %s - Field name.
				$options[ $id . '.longitude' ] = sprintf( __( '%s (longitude)', 'slim-seo' ), $label );
			} else {
				$options[ $id ] = $label;
			}

			if ( ! empty( $field['fields'] ) ) {
				$options = array_merge( $options, $this->add_fields( $field['fields'], $id, $sub_indent ) );
			}
		}
		return $options;
	}

	private function has_value( array $field ): bool {
		return ! in_array( $field['type'], [ 'heading', 'divider', 'custom_html', 'button' ], true );
	}
}
