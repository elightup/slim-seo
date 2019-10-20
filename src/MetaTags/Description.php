<?php
namespace SlimSEO\MetaTags;

class Description {
	public function __construct() {
		add_action( 'init', [ $this, 'add_excerpt_to_pages' ] );
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
		}

		$description = apply_filters( 'slim_seo_meta_description', $description );
		$description = $this->normalize( $description );

		return $description;
	}

	private function normalize( $description ) {
		$description = Helper::normalize( $description );
		$description = wp_trim_words( $description, 60 ); // Recommended length for meta description is 300 characters (~ 60 words).

		return $description;
	}

	private function get_home_description() {
		return is_page() ? $this->get_singular_description() : get_bloginfo( 'description' );
	}

	/**
	 * Get description from post excerpt and fallback to post content.
	 *
	 * @return string
	 */
	private function get_singular_description() {
		$data = get_post_meta( get_the_ID(), 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			return $data['description'];
		}

		$post = get_queried_object();
		return $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	}

	private function get_term_description() {
		$data = get_term_meta( get_queried_object_id(), 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			return $data['description'];
		}

		return get_queried_object()->description;
	}
}
