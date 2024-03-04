<?php
namespace SlimSEO\MetaTags;

class Title {
	use Context;

	public function setup() {
		add_theme_support( 'title-tag' );
		add_filter( 'pre_get_document_title', [ $this, 'filter_title' ] );

		add_filter( 'post_type_archive_title', [ $this, 'set_page_title_as_archive_title' ] );
	}

	public function get_title(): string {
		return wp_get_document_title();
	}

	public function filter_title( $title ): string {
		global $page, $paged;

		$custom_title = $this->get_value();

		// Add a page number if necessary.
		if ( $custom_title && ( $paged >= 2 || $page >= 2 ) ) {
			$separator = apply_filters( 'document_title_separator', '-' ); // phpcs:ignore
			// Translators: %s - Page number.
			$custom_title .= " $separator " . sprintf( __( 'Page %s', 'slim-seo' ), max( $paged, $page ) );
		}

		$title = $custom_title ?: (string) $title;
		$title = apply_filters( 'slim_seo_meta_title', $title, $this->get_queried_object_id() );
		$title = Helper::normalize( $title );

		return $title;
	}

	private function get_home_value(): string {
		$option = get_option( 'slim_seo' );
		return $option['home']['title'] ?? '';
	}

	private function get_post_type_archive_value(): string {
		$post_type_object = get_queried_object();
		$option           = get_option( 'slim_seo' );
		return $option[ "{$post_type_object->name}_archive" ]['title'] ?? '';
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
	public function get_term_value( $term_id = 0 ): string {
		$term_id = $term_id ?: $this->get_queried_object_id();
		$data    = get_term_meta( $term_id, 'slim_seo', true );
		return $data['title'] ?? '';
	}

	public function set_page_title_as_archive_title( string $title ): string {
		return $this->queried_object ? get_the_title( $this->queried_object ) : $title;
	}
}
