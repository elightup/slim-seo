<?php
namespace SlimSEO\MetaTags;

class OpenGraph {
	private $title;
	private $description;
	private $url;
	private $image_obj;

	public function __construct( Title $title, Description $description, CanonicalUrl $url ) {
		$this->title       = $title;
		$this->description = $description;
		$this->url         = $url;
		$this->image_obj   = new Image( 'facebook_image' );
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
		return $this->get_image_attribute( 'alt' );
	}

	private function get_image_attribute( $key ) {
		$image = $this->image_obj->get_value() ?: $this->get_default_image();
		return $image[ $key ] ?? null;
	}

	private function get_default_image() : array {
		$data = get_option( 'slim_seo' );
		return empty( $data['default_facebook_image'] ) ? [] : $this->image_obj->get_data_from_url( $data['default_facebook_image'] );
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
