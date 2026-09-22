<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;
use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Description;

class Homepage extends Settings {
	protected $has_noindex = false;

	protected function ability_config(): array {
		return [
			'slug'               => 'homepage',
			'get_label'          => __( 'Get homepage meta tags', 'slim-seo' ),
			'get_description'    => __( 'Retrieve meta tags for the homepage (title, description and social images).', 'slim-seo' ),
			'update_label'       => __( 'Update homepage meta tags', 'slim-seo' ),
			'update_description' => __( 'Update meta tags for the homepage. Only provided fields are updated.', 'slim-seo' ),
		];
	}

	public function check_permission( array $input ) {
		$error = $this->resolve_input( $input );

		if ( $error ) {
			return $error;
		}

		if ( ! $this->object_id ) {
			return parent::check_permission( $input );
		}

		return current_user_can( 'edit_post', $this->object_id );
	}

	protected function resolve_input( array $input ) {
		if ( $this->context_type ) {
			return null;
		}

		$context = sanitize_key( wp_unslash( $input['context'] ?? '' ) );

		if ( 'home' !== $context ) {
			return new WP_Error( 'slim_seo_abilities_invalid_context', __( 'Invalid homepage context.', 'slim-seo' ) );
		}

		$this->context_type = $context;
		$this->context      = $context;

		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_id = (int) get_option( 'page_on_front' );

			if ( $page_id && get_post( $page_id ) ) {
				$this->object_id   = $page_id;
				$this->object_type = 'post';
			}
		}

		return null;
	}

	protected function get_default(): array {
		if ( ! $this->object_id ) {
			return parent::get_default();
		}

		$settings = $this->get_settings();

		return array_merge(
			[
				'title'       => Title::DEFAULTS['post'] ?? '',
				'description' => Description::DEFAULTS['post'] ?? '',
			],
			$settings['page'] ?? []
		);
	}

	public function get_data( array $input ) {
		$error = $this->resolve_input( $input );

		if ( $error ) {
			return $error;
		}

		if ( ! $this->object_id ) {
			return parent::get_data( $input );
		}

		return $this->normalize_data( $this->get_object_data() );
	}

	public function update_data( array $input ) {
		$error = $this->resolve_input( $input );

		if ( $error ) {
			return $error;
		}

		if ( ! $this->object_id ) {
			return parent::update_data( $input );
		}

		$this->update_object_data( $input );

		return [ 'success' => true ];
	}

	protected function input_props(): array {
		return [
			'context' => [
				'type'        => 'string',
				'description' => __( 'Use "home".', 'slim-seo' ),
			],
		];
	}
}
