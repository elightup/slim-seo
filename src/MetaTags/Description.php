<?php
namespace SlimSEO\MetaTags;

class Description {
	use Context;

	private $is_manual = false;

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

	public function get_description() {
		$description = $this->get_value();
		$description = apply_filters( 'slim_seo_meta_description', $description, $this );
		$description = $this->normalize( $description );

		return $description;
	}

	private function normalize( $description ) {
		$description = Helper::normalize( $description );
		return $this->is_manual ? $description : $this->truncate( $description );
	}

	private function truncate( $string ) {
		return function_exists( 'mb_substr' ) ? mb_substr( $string, 0, 160 ) : substr( $string, 0, 160 );
	}

	private function get_home_value() {
		$data = get_option( 'slim_seo' );
		return empty( $data['home_description'] ) ? get_bloginfo( 'description' ) : $data['home_description'];
	}

	/**
	 * Get custom meta description or fallback to post excerpt or post content
	 * Make public to allow access from other class. See Integration/WooCommerce.
	 */
	public function get_singular_value( $post_id = null ) {
		$post_id = $post_id ?: get_queried_object_id();
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		// Prevent showing description on password protected posts
		if ( post_password_required( $post ) ) {
			return __( 'There is no excerpt because this is a protected post.', 'slim-seo' );
		}

		$data = get_post_meta( $post_id, 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			$this->is_manual = true;
			return $data['description'];
		}

		$description = $post->post_excerpt ? $post->post_excerpt : $post->post_content;

		return apply_filters( 'slim_seo_meta_description_generated', $description );
	}

	private function get_term_value() {
		$data = get_term_meta( get_queried_object_id(), 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			$this->is_manual = true;
			return $data['description'];
		}

		return get_queried_object()->description;
	}

	private function get_author_value() {
		return get_user_meta( get_queried_object_id(), 'description', true );
	}
}
