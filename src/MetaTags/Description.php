<?php
namespace SlimSEO\MetaTags;

defined( 'ABSPATH' ) || die;

use WP_Term;
use SlimSEO\Helpers\Option;

class Description {
	use Context;

	public function setup() {
		$this->add_excerpt_to_pages();
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	/**
	 * Add excerpt to pages to let users customize meta description.
	 */
	public function add_excerpt_to_pages() {
		add_post_type_support( 'page', 'excerpt' );
	}

	public function output() {
		$description = $this->get_description();
		if ( $description ) {
			echo '<meta name="description" content="', esc_attr( $description ), '">', "\n";
		}
	}

	public function get_description(): string {
		$description = $this->get_value();
		$description = apply_filters( 'slim_seo_meta_description', $description, $this->get_queried_object_id() );
		$description = Helper::render( $description );

		return $description;
	}

	private function get_home_value(): string {
		return Option::get( 'home.description', get_bloginfo( 'description' ) );
	}

	private function get_post_type_archive_value(): string {
		$post_type_object = get_queried_object();
		return Option::get( "{$post_type_object->name}_archive.description", '' );
	}

	/**
	 * Get custom meta description or fallback to post excerpt or post content
	 * Make public to allow access from other class. See Integration/WooCommerce.
	 */
	public function get_singular_value( $post_id = null ): string {
		$post_id = $post_id ?: $this->get_queried_object_id();
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		// Prevent showing description on password protected posts
		if ( post_password_required( $post ) ) {
			return __( 'There is no excerpt because this is a protected post.', 'slim-seo' );
		}

		// Use manual entered meta description if available.
		$data = get_post_meta( $post_id, 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			return $data['description'];
		}

		// Use post types settings if avaiable
		$option    = get_option( 'slim_seo', [] );
		$post_type = get_post_type( $post_id );
		if ( ! empty( $option[ $post_type ]['description'] ) ) {
			return $option[ $post_type ]['description'];
		}

		// Use post excerpt if available.
		if ( $post->post_excerpt ) {
			return $post->post_excerpt;
		}

		// Use post content (which page builders can change) at last.
		return apply_filters( 'slim_seo_meta_description_generated', $post->post_content, $post );
	}

	public function get_term_value( $term_id = null ): string {
		$term_id = $term_id ?: $this->get_queried_object_id();
		$data    = get_term_meta( $term_id, 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			return $data['description'];
		}

		$term = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return '';
		}

		// Use taxonomy settings if avaiable, then fallback to the term description
		return Option::get( "{$term->taxonomy}.description", $term->description );
	}

	private function get_author_value(): string {
		// Use author settings if avaiable, then fallback to the author description
		return Option::get( 'author.description', get_user_meta( get_queried_object_id(), 'description', true ) );
	}
}
