<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;

class HomepageStatic extends Post {
	protected function ability_config(): array {
		return [
			'slug'               => 'homepage',
			'get_label'          => __( 'Get homepage meta tags', 'slim-seo' ),
			'get_description'    => __( 'Retrieve meta tags for the homepage (title, description, social images, canonical URL and noindex).', 'slim-seo' ),
			'update_label'       => __( 'Update homepage meta tags', 'slim-seo' ),
			'update_description' => __( 'Update meta tags for the homepage. Only provided fields are updated.', 'slim-seo' ),
		];
	}

	protected function resolve_input( array $input ) {
		$this->reset_state();

		$page_id = (int) get_option( 'page_on_front' );
		$page    = $page_id ? get_post( $page_id ) : null;

		if ( ! $page ) {
			return new WP_Error( 'slim_seo_abilities_homepage_not_found', __( 'The homepage does not exist.', 'slim-seo' ) );
		}

		$this->object_id = $page->ID;

		return null;
	}

	protected function input_props(): array {
		return [];
	}

	protected function input_props_required(): array {
		return [];
	}
}
