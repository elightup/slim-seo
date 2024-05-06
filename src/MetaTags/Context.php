<?php
namespace SlimSEO\MetaTags;

use SlimSEO\Helpers\Data;

trait Context {
	private $queried_object;
	private $queried_object_id;

	public function get_value() {
		if ( is_front_page() ) {
			if ( ! $this->is_static_homepage() ) {
				return $this->get_home_value();
			}

			$this->queried_object_id = get_option( 'page_on_front' );
			$this->queried_object    = get_post( $this->queried_object_id );

			return $this->get_singular_value();
		}

		// If a page is set as the post type archive (like WooCommerce shop), then get value from that page.
		// Otherwise get from the post type archive settings.
		if ( is_post_type_archive() ) {
			$post_type_object = get_queried_object();
			$archive_page     = Data::get_post_type_archive_page( $post_type_object->name );

			if ( ! $archive_page ) {
				return $this->get_post_type_archive_value();
			}

			$this->queried_object    = $archive_page;
			$this->queried_object_id = $archive_page->ID;
			return $this->get_singular_value();
		}

		if ( is_tax() || is_category() || is_tag() ) {
			return $this->get_term_value();
		}

		if ( is_home() || is_singular() ) {
			return $this->get_singular_value();
		}

		if ( is_author() ) {
			return $this->get_author_value();
		}

		return '';
	}

	private function get_home_value() {
		return '';
	}

	private function get_singular_value() {
		return '';
	}

	private function get_post_type_archive_value() {
		return '';
	}

	private function get_term_value() {
		return '';
	}

	private function get_author_value() {
		return '';
	}

	private function get_queried_object() {
		return $this->queried_object ?: get_queried_object();
	}

	private function get_queried_object_id() {
		return $this->queried_object_id ?: get_queried_object_id();
	}

	private function is_static_homepage(): bool {
		return get_option( 'show_on_front' ) === 'page' && get_option( 'page_on_front' );
	}
}
