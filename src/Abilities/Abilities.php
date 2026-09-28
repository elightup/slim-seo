<?php
namespace SlimSEO\Abilities;

class Abilities {
	public function setup(): void {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		add_action( 'wp_abilities_api_categories_init', [ $this, 'register_category' ] );
	}

	public function register_category(): void {
		if ( wp_has_ability_category( 'slim-seo' ) ) {
			return;
		}

		wp_register_ability_category( 'slim-seo', [
			'label'       => __( 'Slim SEO', 'slim-seo' ),
			'description' => __( 'Abilities for Slim SEO data.', 'slim-seo' ),
		] );
	}
}
