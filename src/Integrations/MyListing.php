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
		$data = $this->get_term_data();

		if ( $data ) {
			return $data[0]['title'] ?: '';
		}

		return $title;
	}

	public function update_meta_description( $description ) {
		$data = $this->get_term_data();

		if ( $data ) {
			return $data[0]['description'] ? $data[0]['description'] : ( $data[1]->description ? $data[1]->description : '' );
		}

		return $description;
	}

	private function get_term_data( ) {
		if ( ! get_query_var( 'explore_tab' ) ) {
			return false;
		}

		if ( 'regions' === get_query_var( 'explore_tab' ) ) {
			$term = get_term_by( 'slug', get_query_var( 'explore_region' ), 'region');
		}
		if ( 'categories' === get_query_var( 'explore_tab' ) ) {
			$term = get_term_by( 'slug', get_query_var( 'explore_category' ), 'job_listing_category');
		}
		if ( 'tags' === get_query_var( 'explore_tab' ) ) {
			$term = get_term_by( 'slug', get_query_var( 'explore_tag' ), 'case27_job_listing_tags');
		}

		if ( $term && ! is_wp_error( $term ) ) {
			return [ get_term_meta( $term->term_id, 'slim_seo', true ), $term ];
		}

		return false ;
	}
}