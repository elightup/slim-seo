<?php
namespace SlimSEO\MetaTags;

class Description {
	public function __construct() {
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	public function output() {
		$description = $this->get_description();
		if ( $description ) {
			echo '<meta name="description" content="', esc_attr( $description ), '">', "\n";
		}
	}

	public function get_description() {
		if ( is_front_page() ) {
			return $this->home_description();
		}
		if ( is_tax() || is_category() || is_tag() ) {
			return $this->term_description();
		}
		if ( is_home() || is_singular() ) {
			return $this->singular_description();
		}

		return '';
	}

	private function home_description() {
		return is_page() ? $this->singular_description() : get_bloginfo( 'description' );
	}

	/**
	 * Get description from post excerpt and fallback to post content.
	 * Recommended length for meta description is 300 characters (~ 60 words).
	 *
	 * @return string
	 */
	private function singular_description() {
		$post = get_queried_object();
		return $post->post_excerpt ? $post->post_excerpt : wp_trim_words( $post->post_content, 60 );
	}

	private function term_description() {
		return get_queried_object()->description;
	}
}
