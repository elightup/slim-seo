<?php
namespace SlimSEO\Settings\MetaTags;

use SlimSEO\Helpers\Assets;
use SlimSEO\Helpers\Data;
use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Title;

class Manager {
	private $defaults = [
		'title'          => '',
		'description'    => '',
		'facebook_image' => '',
		'twitter_image'  => '',
	];

	public function enqueue(): void {
		wp_enqueue_media();

		wp_enqueue_style( 'slim-seo-react-tabs', SLIM_SEO_URL . 'css/react-tabs.css', [], filemtime( SLIM_SEO_DIR . '/css/react-tabs.css' ) );
		wp_enqueue_style( 'slim-seo-meta-tags', SLIM_SEO_URL . 'css/meta-tags.css', [ 'wp-components' ], filemtime( SLIM_SEO_DIR . '/css/meta-tags.css' ) );
		Assets::enqueue_build_js( 'meta-tags', 'ss', [
			'hasHomepageSettings'      => $this->has_homepage_settings(),
			'homepage'                 => $this->get_home_data(),
			'postTypes'                => Data::get_post_types(),
			'taxonomies'               => Data::get_taxonomies(),
			'postTypesWithArchivePage' => $this->get_post_types_with_archive_page(),
			'defaultPostMetas'         => $this->get_default_post_metas(),
			'defaultTermMetas'         => $this->get_default_term_metas(),
			'defaultAuthorMetas'       => $this->get_default_author_metas(),
			'mediaPopupTitle'          => __( 'Select An Image', 'slim-seo' ),
		] );
	}

	private function has_homepage_settings(): bool {
		return 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' );
	}

	private function get_home_data(): array {
		return array_merge( $this->defaults, [
			'link'        => get_home_url(),
			'name'        => get_the_title( get_option( 'page_on_front' ) ),
			'title'       => Title::DEFAULTS['home'],
			'description' => Description::DEFAULTS['home'],
			'edit'        => get_edit_post_link( get_option( 'page_on_front' ) ),
		] );
	}

	private function get_content_items(): array {
		$taxonomies = array_keys( array_filter( Data::get_taxonomies() ) );
		$post_types = array_keys( array_filter( Data::get_post_types() ) );

		$post_types_archive = array_map( function ( $post_type ) {
			return "{$post_type}_archive";
		}, $post_types );

		$items   = array_merge(
			$post_types,
			$post_types_archive,
			$taxonomies
		);
		$items[] = 'home';
		$items[] = 'author';

		return $items;
	}

	public function sanitize( array &$option, array $data ): void {
		$items = $this->get_content_items();
		foreach ( $items as $item ) {
			if ( empty( $data[ $item ] ) || ! is_array( $data[ $item ] ) || empty( $option[ $item ] ) || ! is_array( $option[ $item ] ) ) {
				unset( $option[ $item ] );
				continue;
			}

			$temp = $this->sanitize_item( $option[ $item ] );
			if ( empty( $temp ) ) {
				unset( $option[ $item ] );
			} else {
				$option[ $item ] = $temp;
			}
		}
	}

	private function sanitize_item( array $data ): array {
		$data = array_merge( $this->defaults, $data );

		$data['title']          = sanitize_text_field( $data['title'] );
		$data['description']    = sanitize_text_field( $data['description'] );
		$data['facebook_image'] = sanitize_text_field( $data['facebook_image'] );
		$data['twitter_image']  = sanitize_text_field( $data['twitter_image'] );

		return array_filter( $data );
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

	private function get_default_post_metas(): array {
		return [
			'single'  => [
				'title'       => Title::DEFAULTS['post'],
				'description' => Description::DEFAULTS['post'],
			],
			'archive' => [
				'title'       => Title::DEFAULTS['post_archive'],
				'description' => Description::DEFAULTS['post_archive'],
			],
		];
	}

	private function get_default_term_metas(): array {
		return [
			'title'       => Title::DEFAULTS['term'],
			'description' => Description::DEFAULTS['term'],
		];
	}

	private function get_default_author_metas(): array {
		return [
			'title'       => Title::DEFAULTS['author'],
			'description' => Description::DEFAULTS['author'],
		];
	}
}
