<?php
namespace SlimSEO\MetaTags;

class OpenGraph {
	private $title;
	private $description;
	private $url;

	public function __construct( Title $title, Description $description, CanonicalUrl $url ) {
		$this->title       = $title;
		$this->description = $description;
		$this->url         = $url;
	}

	public function setup() {
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	public function output() {
		$properties = apply_filters( 'slim_seo_open_graph_tags', [
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
			'article:author',
			'fb:app_id',
		] );
		foreach ( $properties as $property ) {
			$short_name = strtr( $property, [
				'og:' => '',
				'fb:' => '',
				':'   => '_',
			] );
			$getter     = "get_{$short_name}";
			$value      = method_exists( $this, $getter ) ? $this->$getter() : '';
			$value      = apply_filters( "slim_seo_open_graph_{$short_name}", $value, $property );
			$this->output_tag( $property, $value );
		}
	}

	private function get_title() {
		return $this->title->get_title();
	}

	private function get_type() {
		return is_singular() ? 'article' : 'website';
	}

	private function get_image() {
		return $this->get_image_attribute( 'src' );
	}

	private function get_image_width() {
		return $this->get_image_attribute( 'width' );
	}

	private function get_image_height() {
		return $this->get_image_attribute( 'height' );
	}

	private function get_image_alt() {
		if ( ! is_singular() ) {
			return null;
		}
		return get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
	}

	private function get_image_attribute( $key ) {
		static $image;
		static $ran = false;

		if ( ! $ran ) {
			$default_image = $this->get_default_image();
			$image_obj     = new Image( 'facebook_image' );
			$image         = $image_obj->get_value() ?: $default_image;
			$ran           = true;
		}

		$keys = [
			'src'    => 0,
			'width'  => 1,
			'height' => 2,
		];

		return isset( $image[ $keys[ $key ] ] ) ? $image[ $keys[ $key ] ] : null;
	}

	private function get_default_image() {
		$data = get_option( 'slim_seo' );
		if ( empty( $data['default_facebook_image'] ) ) {
			return null;
		}
		$image_id = attachment_url_to_postid( $data['default_facebook_image'] );
		return $image_id ? wp_get_attachment_image_src( $image_id, 'full' ) : [ $data['default_facebook_image'] ];
	}

	private function get_description() {
		return $this->description->get_description();
	}

	private function get_url() {
		return $this->url->get_url();
	}

	private function get_locale() {
		return get_locale();
	}

	private function get_site_name() {
		return get_bloginfo( 'name' );
	}

	private function get_article_published_time() {
		return is_singular() ? gmdate( 'c', strtotime( get_queried_object()->post_date_gmt ) ) : null;
	}

	private function get_article_modified_time() {
		return is_singular() ? gmdate( 'c', strtotime( get_queried_object()->post_modified_gmt ) ) : null;
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

	private function get_article_author() {
		return is_singular() ? get_the_author_meta( 'display_name', get_queried_object()->post_author ) : null;
	}

	private function get_app_id() {
		$data = get_option( 'slim_seo' );
		return empty( $data['facebook_app_id'] ) ? null : $data['facebook_app_id'];
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
