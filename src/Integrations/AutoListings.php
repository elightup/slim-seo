<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Title;

class AutoListings {
	private $archive_page_id;

	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! defined( 'AUTO_LISTINGS_VERSION' ) ) {
			return;
		}

		// Only process the listing archive page.
		if ( ! is_post_type_archive( 'auto-listing' ) ) {
			return;
		}

		$this->archive_page_id = (int) auto_listings_option( 'archives_page' );

		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
		add_filter( 'post_type_archive_title', [ $this, 'set_page_title_as_archive_title' ] );
		add_filter( 'slim_seo_meta_title', [ $this, 'archive_title' ], 10, 2 );
		add_filter( 'slim_seo_meta_description', [ $this, 'archive_description' ], 10, 2 );
		add_filter( 'slim_seo_robots_index', [ $this, 'archive_index' ] );
	}

	public function skip_shortcodes( array $shortcodes ) : array {
		$shortcodes = array_merge( $shortcodes, [
			'auto_listings_search',
			'auto_listings_listing',
			'auto_listings_listings',
			'auto_listings_contact_form',
			'als_button',
			'als_total_listings',
			'als_selected',
			'als_toggle_wrapper',
			'als_keyword',
			'als_field',
			'als',
		] );
		return $shortcodes;
	}

	public function set_page_title_as_archive_title() : string {
		return get_the_title( $this->archive_page_id );
	}

	public function archive_title( $title, Title $title_obj ) {
		return $title_obj->get_singular_value( $this->archive_page_id ) ?: $title;
	}

	public function archive_description( $description, Description $description_obj ) {
		return $description_obj->get_singular_value( $this->archive_page_id ) ?: $description;
	}

	public function archive_index( $is_indexed ) {
		$data = get_post_meta( $this->archive_page_id, 'slim_seo', true );
		return empty( $data['noindex'] ) ? $is_indexed : false;
	}
}
