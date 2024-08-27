<?php
namespace SlimSEO\Settings\Content;

use SlimSEO\Helpers\Data;

class Manager {
	private $items = [];

	public function setup() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_init() {
		if ( $this->has_homepage_settings() ) {
			$this->items[ 'home' ] = new Homepage;
		}
	}

	public function get_post_types(): array {
		return array_diff_key( Data::get_post_types(), array_flip( [ 'post', 'page' ] ) );
	}

	public function get( string $name ): Item {
		return $this->items[ $name ];
	}

	public function enqueue() {
		wp_enqueue_media();
		wp_enqueue_style( 'slim-seo-meta-tags', SLIM_SEO_URL . 'css/meta-tags.css', [], filemtime( SLIM_SEO_DIR . '/css/meta-tags.css' ) );

		wp_enqueue_style( 'slim-seo-post-types', SLIM_SEO_URL . 'css/content.css', [], filemtime( SLIM_SEO_DIR . '/css/content.css' ) );
		wp_enqueue_script( 'slim-seo-post-types', SLIM_SEO_URL . 'js/content.js', [ 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch' ], filemtime( SLIM_SEO_DIR . 'js/content.js' ), true );
		wp_localize_script( 'slim-seo-post-types', 'ss', [
			'mediaPopupTitle'          => __( 'Select An Image', 'slim-seo' ),
			'site'            => [
				'title'       => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ),
				'description' => html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES, 'UTF-8' ),
			],
		] );
		wp_localize_script( 'slim-seo-post-types', 'ssContent', [
			'hasHomepageSettings'      => $this->has_homepage_settings(),
			'homepage'                 => $this->has_homepage_settings() ? $this->items[ 'home' ]->get_data() : [],
			'postTypes'                => $this->get_post_types(),
			'taxonomies'               => $this->get_taxonomies(),
			'postTypesWithArchivePage' => $this->get_post_types_with_archive_page(),
		] );
	}

	public function has_homepage_settings() {
		return 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' );
	}

	public function sanitize( array &$option, array $data ) {
		// Post type settings.
		$post_types = array_keys( $this->get_post_types() );
		foreach ( $post_types as $post_type ) {
			if ( empty( $data[ $post_type ] ) ) {
				unset( $option[ $post_type ] );
			}
		}

		// Post type archive settings.
		foreach ( $this->items as $key => $item ) {
			if ( ! isset( $option[ $key ] ) ) {
				continue;
			}
			$temp = $item->sanitize( $option[ $key ] );
			if ( empty( $temp ) ) {
				unset( $option[ $key ] );
			} else {
				$option[ $key ] = $temp;
			}
		}
	}

	private function get_post_types_with_archive_page(): array {
		$post_types = $this->get_post_types();

		if ( ! $post_types ) {
			return [];
		}

		$archive = [];
		foreach ( $post_types as $key => $post_type ) {
			$archive_page = Data::get_post_type_archive_page( $key );
			if ( $archive_page ) {
				$archive[ $key ] = [
					'link'  => get_permalink( $archive_page ),
					'title' => $archive_page->post_title,
					'edit'  => get_edit_post_link( $archive_page ),
				];
			}
		}

		return $archive;
	}

	private function get_taxonomies() {
		$unsupported = [
			'wp_theme',
			'wp_template_part_area',
			'link_category',
			'nav_menu',
			'post_format',
			'mb-views-category',
		];
		$taxonomies  = get_taxonomies( [ 'public' => true ], 'objects' );
		return array_diff_key( $taxonomies, array_flip( $unsupported ) );
	}
}
