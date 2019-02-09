<?php
namespace SlimSEO\MetaTags;

class OpenGraph {
	private $title;
	private $description;

	public function __construct( Title $title, Description $description ) {
		$this->title       = $title;
		$this->description = $description;

		add_filter( 'jetpack_enable_open_graph', '__return_false' );
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	public function output() {
		$properties = [
			'og:title',
			'og:type',
			'og:image',
			'og:image:width',
			'og:image:height',
			'og:image:alt',
			'og:description',
			'og:url',
			'og:locale',
			'og:site_name',
			'article:published_time',
			'article:modified_time',
			'og:updated_time',
			'article:section',
			'article:tag',
		];
		foreach ( $properties as $property ) {
			$getter = 'get_' . strtr(
				$property,
				[
					'og:' => '',
					':'   => '_',
				]
			);
			$this->output_tag( $property, $this->$getter() );
		}
	}

	private function get_title() {
		return $this->title->get_title();
	}

	private function get_type() {
		return is_singular() ? 'article' : 'website';
	}

	private function get_image() {
		return $this->get_thumbnail_attribute( 'src' );
	}

	private function get_image_width() {
		return $this->get_thumbnail_attribute( 'width' );
	}

	private function get_image_height() {
		return $this->get_thumbnail_attribute( 'height' );
	}

	private function get_image_alt() {
		if ( ! is_singular() ) {
			return null;
		}
		return get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
	}

	private function get_thumbnail_attribute( $key ) {
		static $thumbnail;
		static $ran = false;

		if ( ! $ran && is_singular() ) {
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
			$ran       = true;
		}

		$keys = [
			'src'    => 0,
			'width'  => 1,
			'height' => 2,
		];

		return isset( $thumbnail[ $keys[ $key ] ] ) ? $thumbnail[ $keys[ $key ] ] : null;
	}

	private function get_description() {
		return $this->description->get_description();
	}

	private function get_url() {
		return is_singular() ? get_permalink() : null;
	}

	private function get_locale() {
		return get_locale();
	}

	private function get_site_name() {
		return get_bloginfo( 'name' );
	}

	private function get_article_published_time() {
		return is_singular() ? date( 'c', strtotime( get_queried_object()->post_date_gmt ) ) : null;
	}

	private function get_article_modified_time() {
		return is_singular() ? date( 'c', strtotime( get_queried_object()->post_modified_gmt ) ) : null;
	}

	private function get_updated_time() {
		return $this->get_article_modified_time();
	}

	private function get_article_section() {
		if ( ! is_single() || 'post' !== get_post_type() ) {
			return null;
		}
		$categories = get_the_category();
		if ( empty( $categories ) ) {
			return null;
		}
		$category = reset( $categories );
		return $category->name;
	}

	private function get_article_tag() {
		if ( ! is_single() || 'post' !== get_post_type() ) {
			return null;
		}
		$tags = get_the_tags();
		return is_array( $tags ) ? wp_list_pluck( $tags, 'name' ) : null;
	}

	private function output_tag( $property, $content ) {
		if ( ! $content ) {
			return;
		}
		$content = (array) $content;
		foreach ( $content as $value ) {
			echo '<meta property="', esc_attr( $property ), '" content="', esc_attr( $value ), '">', "\n";
		}
	}
}
