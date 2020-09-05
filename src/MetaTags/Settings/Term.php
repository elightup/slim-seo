<?php
namespace SlimSEO\MetaTags\Settings;

class Term extends Base {
	public function setup() {
		$this->object_type = 'term';
		$this->title       = __( 'Search Engine Optimization', 'slim-seo' );

		// Prority 99 makes sure all taxonomies are registered.
		add_action( 'init', [ $this, 'register_hooks' ], 99 );
	}

	public function register_hooks() {
		add_action( 'admin_print_styles-term.php', [ $this, 'enqueue' ] );

		$taxonomies = $this->get_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "{$taxonomy}_edit_form", [ $this, 'render' ] );
			add_action( "edited_$taxonomy", [ $this, 'save' ] );
		}
	}

	private function get_taxonomies() {
		return get_taxonomies( [ 'public' => true ] );
	}

	protected function get_object_id() {
		return filter_input( INPUT_GET, 'tag_ID', FILTER_SANITIZE_NUMBER_INT );
	}
}