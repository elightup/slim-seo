<?php
namespace SlimSEO\MetaTags\Settings;

class Term extends Base {
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

	public function save( $term_id ) {
		$data = $this->get_form_data();
		if ( null !== $data ) {
			update_term_meta( $term_id, 'slim_seo', $data );
		}
	}

	protected function get_data() {
		$term_id = filter_input( INPUT_GET, 'tag_ID', FILTER_SANITIZE_NUMBER_INT );
		$data    = get_term_meta( $term_id, 'slim_seo', true );
		$data    = $data ? $data : [];

		return array_merge( $this->defaults, $data );
	}
}