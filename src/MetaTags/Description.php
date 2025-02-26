<?php
namespace SlimSEO\MetaTags;

defined( 'ABSPATH' ) || die;

use WP_Term;
use SlimSEO\Helpers\Option;

class Description {
	use Context;

	private $is_manual = false;

	const DEFAULTS = [
		'home'         => '{{ site.description }}',
		'post'         => '{{ post.auto_description }}',
		'post_archive' => '',
		'term'         => '{{ term.auto_description }}',
		'author'       => '{{ author.auto_description }}',
	];

	public function setup(): void {
		$this->add_excerpt_to_pages();
		add_action( 'slim_seo_head', [ $this, 'output' ] );
	}

	/**
	 * Add excerpt to pages to let users customize meta description.
	 */
	public function add_excerpt_to_pages(): void {
		add_post_type_support( 'page', 'excerpt' );
	}

	public function output(): void {
		$description = $this->get_description();
		if ( $description ) {
			echo '<meta name="description" content="', esc_attr( $description ), '">', "\n";
		}
	}

	public function get_description(): string {
		$description = $this->get_value();
		$description = (string) apply_filters( 'slim_seo_meta_description', $description, $this->get_queried_object_id() );
		$description = Helper::render( $description );

		return $description;
	}

	private function get_home_value(): string {
		return Option::get( 'home.description', self::DEFAULTS['home'] );
	}

	private function get_post_type_archive_value(): string {
		$post_type_object = get_queried_object();
		return Option::get( "{$post_type_object->name}_archive.description", self::DEFAULTS['post_archive'] );
	}

	private function get_singular_value( int $post_id = 0 ): string {
		$this->is_manual = false;

		$post_id = $post_id ?: $this->get_queried_object_id();
		// Prevent showing description on password protected posts
		if ( post_password_required( $post_id ) ) {
			return '';
		}

		// Use manual entered meta description if available.
		$data = get_post_meta( $post_id, 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			$this->is_manual = true;
			return $data['description'];
		}

		$post_type = get_post_type( $post_id );

		// Use post type settings if avaiable, then fallback to the post auto description
		return Option::get( "$post_type.description", self::DEFAULTS['post'] );
	}

	/**
	 * Get the rendered meta description, after parsing dynamic variables
	 * Make public to allow access from other class.
	 * @see \SlimSEO\MetaTags\AdminColumns\Post::render()
	 * @see \SlimSEO\RestApi::prepare_value_for_post()
	 */
	public function get_rendered_singular_value( int $post_id = 0 ): string {
		$description = $this->get_singular_value( $post_id );
		$description = (string) apply_filters( 'slim_seo_meta_description', $description, $post_id );
		$description = Helper::render( $description, $post_id );

		return $description;
	}

	public function get_term_value( $term_id = null ): string {
		$this->is_manual = false;

		$term_id = $term_id ?: $this->get_queried_object_id();
		$data    = get_term_meta( $term_id, 'slim_seo', true );
		if ( ! empty( $data['description'] ) ) {
			$this->is_manual = true;
			return $data['description'];
		}

		$term = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return '';
		}

		// Use taxonomy settings if avaiable, then fallback to the term auto description
		return Option::get( "{$term->taxonomy}.description", self::DEFAULTS['term'] );
	}

	/**
	 * Get the rendered meta description, after parsing dynamic variables
	 * Make public to allow access from other class.
	 * @see \SlimSEO\MetaTags\AdminColumns\Term::render()
	 */
	public function get_rendered_term_value( int $term_id = 0 ): string {
		$description = $this->get_term_value( $term_id );
		$description = (string) apply_filters( 'slim_seo_meta_description', $description, $term_id );
		$description = Helper::render( $description, 0, $term_id );

		return $description;
	}

	private function get_author_value(): string {
		// Use author settings if avaiable, then fallback to the author auto description
		return Option::get( 'author.description', self::DEFAULTS['author'] );
	}

	public function check_is_manual(): bool {
		return $this->is_manual;
	}
}
