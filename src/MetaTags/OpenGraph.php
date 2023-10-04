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

	public function setup(): void {
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	public function output(): void {
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

	private function get_title(): string {
		return $this->title->get_title();
	}

	private function get_type(): string {
		return is_singular() && ! is_front_page() ? 'article' : 'website';
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

	private function get_default_image(): array {
		$option = get_option( 'slim_seo' );
		return empty( $option['default_facebook_image'] ) ? [] : $this->image_obj->get_data_from_url( $option['default_facebook_image'] );
	}

	private function get_description(): string {
		return $this->description->get_description();
	}

	private function get_url(): string {
		return $this->url->get_url();
	}

	private function get_locale(): string {
		return get_locale();
	}

	private function get_site_name(): string {
		return get_bloginfo( 'name' );
	}

	private function get_article_published_time(): string {
		return is_singular() && ! is_front_page() ? gmdate( 'c', strtotime( get_queried_object()->post_date_gmt ) ) : '';
	}

	private function get_article_modified_time(): string {
		return is_singular() && ! is_front_page() ? gmdate( 'c', strtotime( get_queried_object()->post_modified_gmt ) ) : '';
	}

	private function get_updated_time(): string {
		return $this->get_article_modified_time();
	}

	private function get_article_section(): string {
		if ( ! is_single() || 'post' !== get_post_type() ) {
			return '';
		}
		$categories = get_the_category();
		if ( empty( $categories ) ) {
			return '';
		}
		$category = reset( $categories );
		return $category->name;
	}

	private function get_article_tag(): array {
		if ( ! is_single() || 'post' !== get_post_type() ) {
			return [];
		}
		$tags = get_the_tags();
		return is_array( $tags ) ? wp_list_pluck( $tags, 'name' ) : [];
	}

	private function get_app_id(): string {
		$data = get_option( 'slim_seo' );
		return empty( $data['facebook_app_id'] ) ? '' : $data['facebook_app_id'];
	}

	private function output_tag( string $property, $content ) {
		if ( ! $content ) {
			return;
		}
		$content = (array) $content;
		foreach ( $content as $value ) {
			echo '<meta property="', esc_attr( $property ), '" content="', esc_attr( $value ), '">', "\n";
		}
	}
}
