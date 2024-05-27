<?php
namespace SlimSEO\Integrations;

class MyListing {

	public function is_active(): bool {
		return ( 'my-listing' === get_template() );
	}

	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		add_filter( 'slim_seo_meta_title', [ $this, 'update_meta_title' ] );
		add_filter( 'slim_seo_meta_description', [ $this, 'update_meta_description' ] );
	}

	public function update_meta_title( $title ) {
		if ( ! get_query_var( 'explore_tab' ) ) {
			return $title;
		}

		list( $data, $term ) = $this->get_listing_data();
		return $data['title'] ?: '';
	}

	public function update_meta_description( $description ) {
		if ( ! get_query_var( 'explore_tab' ) ) {
			return $description;
		}

		list( $data, $term ) = $this->get_listing_data();
		return $data['description'] ? $data['description'] : ( $term && ! is_wp_error( $term ) ? $term->description : '' );
	}

	private function get_listing_data() {
		if ( 'regions' === get_query_var( 'explore_tab' ) ) {
			$term = get_term_by( 'slug', get_query_var( 'explore_region' ), 'region');
		}
		if ( 'categories' === get_query_var( 'explore_tab' ) ) {
			$term = get_term_by( 'slug', get_query_var( 'explore_category' ), 'job_listing_category');
		}

		$data = get_term_meta( $term->term_id, 'slim_seo', true );
		return [ $data, $term ];
	}
}