<?php
namespace SlimSEO\MetaTags\Abilities;

class HomepageDefault extends Settings {
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

	protected function resolve_input( array $input ) {
		$this->reset_state();

		$this->context      = 'home';
		$this->context_type = 'home';

		return null;
	}

	protected function input_props(): array {
		return [];
	}

	protected function input_props_required(): array {
		return [];
	}
}
