<?php
namespace SlimSEO\MetaTags\Settings;

class Term extends Base {
	protected $object_type = 'post';

	public function __construct() {
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