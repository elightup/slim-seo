<?php
namespace SlimSEO\MetaTags;

class Title {
	use Context;

	public function setup() {
		add_theme_support( 'title-tag' );
		add_filter( 'pre_get_document_title', [ $this, 'filter_title' ] );

		add_filter( 'post_type_archive_title', [ $this, 'set_page_title_as_archive_title' ] );
	}

	public function get_title() : string {
		return wp_get_document_title();
	}

	public function filter_title( $title ) : string {
		$custom_title = $this->get_value();

		$title = $custom_title ?: (string) $title;
		$title = apply_filters( 'slim_seo_meta_title', $title, $this->get_queried_object_id() );
		$title = Helper::normalize( $title );

		return $title;
	}

	private function get_home_value() : string {
		$data = get_option( 'slim_seo' );
		return $data['home_title'] ?? '';
	}

	/**
	 * Make public to allow access from other class.
	 * @see AdminColumns/Post.php.
	 */
	public function get_singular_value( $post_id = 0 ): string {
		$post_id = $post_id ?: $this->get_queried_object_id();
		$data    = get_post_meta( $post_id, 'slim_seo', true );
		return $data['title'] ?? '';
	}

	/**
	 * Make public to allow access from other class.
	 * @see AdminColumns/Term.php.
	 */
	public function get_term_value( $term_id = 0 ) : string {
		$term_id = $term_id ?: $this->get_queried_object_id();
		$data    = get_term_meta( $term_id, 'slim_seo', true );
		return $data['title'] ?? '';
	}

	public function set_page_title_as_archive_title( string $title ): string {
		return $this->queried_object ? get_the_title( $this->queried_object ) : $title;
	}
}
