<?php
namespace SlimSEO\Settings\Content;

use SlimSEO\Helpers\Data;

class Manager {
	private $items = [];

	public function setup() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_init() {
		$items = $this->get_content_items();

		foreach ( $items as $item ) {
			$this->items[ $item ] = new Item( $item );
		}
	}

	public function get( string $name ): Item {
		return $this->items[ $name ];
	}

	public function enqueue() {
		wp_enqueue_media();
		wp_enqueue_style( 'slim-seo-meta-tags', SLIM_SEO_URL . 'css/meta-tags.css', [], filemtime( SLIM_SEO_DIR . '/css/meta-tags.css' ) );

		wp_enqueue_style( 'slim-seo-content', SLIM_SEO_URL . 'css/content.css', [], filemtime( SLIM_SEO_DIR . '/css/content.css' ) );
		wp_enqueue_script( 'slim-seo-content', SLIM_SEO_URL . 'js/content.js', [ 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch' ], filemtime( SLIM_SEO_DIR . 'js/content.js' ), true );
		wp_localize_script( 'slim-seo-content', 'ss', [
			'hasHomepageSettings'      => $this->has_homepage_settings(),
			'homepage'                 => $this->has_homepage_settings() ? $this->items[ 'home' ]->get_home_data() : [],
			'postTypes'                => Data::get_post_types(),
			'taxonomies'               => Data::get_taxonomies(),
			'postTypesWithArchivePage' => $this->get_post_types_with_archive_page(),
			'mediaPopupTitle'          => __( 'Select An Image', 'slim-seo' ),
			'site'            => [
				'title'       => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ),
				'description' => html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES, 'UTF-8' ),
			],
			'title'           => [
				'separator'   => apply_filters( 'document_title_separator', '-' ), // phpcs:ignore
				'parts'       => apply_filters( 'slim_seo_title_parts', [ 'title', 'site' ], 'post' ),
			],
		] );
	}

	public function has_homepage_settings() {
		return 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' );
	}

	private function get_content_items() {
		$taxonomies = array_keys( array_filter( Data::get_taxonomies() ) );
		$post_types = array_keys( array_filter( Data::get_post_types() ) );

		$post_types_archive = array_map( function ( $item ) {
			return "{$item}_archive";
		}, $post_types );

		$items = array_merge(
			$post_types,
			$post_types_archive,
			$taxonomies
		);
		if ( $this->has_homepage_settings() ) {
			$items[] = 'home';
		}

		return $items;
	}

	public function sanitize( array &$option, array $data ) {
		$items = $this->get_content_items();
		foreach ( $items as $item ) {
			if ( empty( $data[ $item ] ) ) {
				unset( $option[ $item ] );
			}
		}

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
		$post_types = Data::get_post_types();

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
}
