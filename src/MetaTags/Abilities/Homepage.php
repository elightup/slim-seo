<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;

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

	protected function resolve_context( array $input ) {
		if ( $this->context_type ) {
			return null;
		}

		$context = sanitize_key( wp_unslash( $input['context'] ?? '' ) );

		if ( 'home' !== $context ) {
			return new WP_Error( 'slim_seo_abilities_invalid_context', __( 'Invalid homepage context.', 'slim-seo' ) );
		}

		$this->context_type = $context;
		$this->context      = $context;

		return null;
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
