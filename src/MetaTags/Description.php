<?php
namespace SlimSEO\MetaTags;

class Description {
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
		$description = '';

		if ( is_front_page() ) {
			$description = $this->get_home_description();
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$description = $this->get_term_description();
		} elseif ( is_home() || is_singular() ) {
			$description = $this->get_singular_description();
		} elseif ( is_author() ) {
			$description = $this->get_author_description();
		}

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

	private function get_home_description() {
		return is_page() ? $this->get_singular_description() : get_bloginfo( 'description' );
	}

	/**
	 * Get description from post excerpt and fallback to post content.
	 * Make public to allow access from other class. See Integration/WooCommerce.
	 */
	public function get_singular_description( $post_id = null ) {
		$post_id = $post_id ?: get_queried_object_id();
		$data = get_post_meta( $post_id, 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			$this->is_manual = true;
			return $data['description'];
		}

		$post = get_post( $post_id );
		return $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	}

	private function get_term_description() {
		$data = get_term_meta( get_queried_object_id(), 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			$this->is_manual = true;
			return $data['description'];
		}

		return get_queried_object()->description;
	}

	private function get_author_description() {
		return get_user_meta( get_queried_object_id(), 'description', true );
	}
}
