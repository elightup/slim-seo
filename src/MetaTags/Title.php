<?php
namespace SlimSEO\MetaTags;

defined( 'ABSPATH' ) || die;

use WP_Term;
use SlimSEO\Helpers\Option;

class Title {
	use Context;

	const DEFAULTS = [
		'home'         => '{{ site.title }} {{ sep }} {{ site.description }}',
		'post'         => '{{ post.title }} {{ page }} {{ sep }} {{ site.title }}',
		'post_archive' => '{{ post_type.labels.plural }} {{ page }} {{ sep }} {{ site.title }}',
		'term'         => '{{ term.name }} {{ page }} {{ sep }} {{ site.title }}',
		'author'       => '{{ author.display_name }} {{ page }} {{ sep }} {{ site.title }}',
	];

	public function setup(): void {
		add_theme_support( 'title-tag' );
		add_filter( 'pre_get_document_title', [ $this, 'filter_title' ] );

		add_filter( 'post_type_archive_title', [ $this, 'set_page_title_as_archive_title' ] );
	}

	public function get_title(): string {
		return wp_get_document_title();
	}

	public function filter_title( $title ): string {
		$custom_title = $this->get_value();
		$title        = $custom_title ?: (string) $title;
		$title        = (string) apply_filters( 'slim_seo_meta_title', $title, $this->get_queried_object_id() );
		$title        = Helper::render( $title );

		return $title;
	}

	private function get_home_value(): string {
		return Option::get( 'home.title', '' );
	}

	private function get_post_type_archive_value(): string {
		$post_type_object = get_queried_object();
		return Option::get( "{$post_type_object->name}_archive.title", '' );
	}

	/**
	 * Get singular post/page/post type meta title.
	 * Note that returning empty string will use WordPress default title.
	 */
	private function get_singular_value( int $post_id = 0 ): string {
		$post_id = $post_id ?: $this->get_queried_object_id();
		$data    = get_post_meta( $post_id, 'slim_seo', true );
		if ( ! empty( $data['title'] ) ) {
			return $data['title'];
		}

		// For static frontpage: don't use page's settings, use WordPress default instead.
		$is_static_frontpage = 'page' === get_option( 'show_on_front' ) && $post_id === (int) get_option( 'page_on_front' );
		if ( $is_static_frontpage ) {
			return '';
		}

		// Get from admin settings for this post type.
		$post_type = get_post_type( $post_id );
		return Option::get( "{$post_type}.title", '' );
	}

	/**
	 * Get the rendered meta title, after parsing dynamic variables
	 * Make public to allow access from other class.
	 * @see \SlimSEO\MetaTags\AdminColumns\Post::render()
	 * @see \SlimSEO\RestApi::prepare_value_for_post()
	 */
	public function get_rendered_singular_value( int $post_id = 0 ): string {
		$title = $this->get_singular_value( $post_id ) ?: self::DEFAULTS['post'];
		$title = (string) apply_filters( 'slim_seo_meta_title', $title, $post_id );
		$title = Helper::render( $title, $post_id );

		return $title;
	}

	/**
	 * Make public to allow access from other class.
	 * @see AdminColumns/Term.php.
	 */
	public function get_term_value( $term_id = 0 ): string {
		$term_id = $term_id ?: $this->get_queried_object_id();
		$data    = get_term_meta( $term_id, 'slim_seo', true );
		if ( ! empty( $data['title'] ) ) {
			return $data['title'];
		}

		$term = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return '';
		}

		return Option::get( "{$term->taxonomy}.title", '' );
	}

	/**
	 * Get the rendered meta title, after parsing dynamic variables
	 * Make public to allow access from other class.
	 * @see \SlimSEO\MetaTags\AdminColumns\Term::render()
	 */
	public function get_rendered_term_value( int $term_id = 0 ): string {
		$title = $this->get_term_value( $term_id ) ?: self::DEFAULTS['term'];
		$title = (string) apply_filters( 'slim_seo_meta_title', $title, $term_id );
		$title = Helper::render( $title, 0, $term_id );

		return $title;
	}

	public function set_page_title_as_archive_title( string $title ): string {
		return $this->queried_object ? get_the_title( $this->queried_object ) : $title;
	}

	private function get_author_value(): string {
		return Option::get( 'author.title', '' );
	}
}
