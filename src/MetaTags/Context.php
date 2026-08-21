<?php
namespace SlimSEO\MetaTags;

use SlimSEO\Helpers\Data;

trait Context {
	private $queried_object;
	private $queried_object_id;

	public function get_value() {
		if ( is_front_page() ) {
			if ( ! Data::has_static_homepage() ) {
				return $this->get_home_value();
			}

			$post_id = (int) get_option( 'page_on_front' );
			QueriedObject::set_id( $post_id );
			QueriedObject::set( get_post( $post_id ) );

			return $this->get_singular_value();
		}

		// If a page is set as the post type archive (like WooCommerce shop), then get value from that page.
		// Otherwise get from the post type archive settings.
		if ( is_post_type_archive() ) {
			$page = get_queried_object();

			if ( ! ( $page instanceof \WP_Post_Type || $page instanceof \WP_Post ) ) {
				return '';
			}

			if ( $page instanceof \WP_Post_Type ) {
				$page = Data::get_post_type_archive_page( $page->name );

				if ( ! $page ) {
					return $this->get_post_type_archive_value();
				}
			}

			QueriedObject::set( $page );
			QueriedObject::set_id( $page->ID );

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
		return QueriedObject::get();
	}

	private function get_queried_object_id() {
		return QueriedObject::get_id();
	}
}
