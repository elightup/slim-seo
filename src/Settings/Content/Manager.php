<?php
namespace SlimSEO\Settings\Content;

use SlimSEO\Helpers\Assets;
use SlimSEO\Helpers\Data;

class Manager {
	private $items = [];

	public function setup() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_init(): void {
		$items = $this->get_content_items();

		foreach ( $items as $item ) {
			$this->items[ $item ] = new Item( $item );
		}
	}

	public function get( string $name ): Item {
		return $this->items[ $name ];
	}

	public function enqueue(): void {
		wp_enqueue_media();

		wp_enqueue_style( 'slim-seo-content', SLIM_SEO_URL . 'css/content.css', [], filemtime( SLIM_SEO_DIR . '/css/content.css' ) );
		Assets::enqueue_build_js( 'content', 'ss', [
			'hasHomepageSettings'      => $this->has_homepage_settings(),
			'homepage'                 => $this->items['home']->get_home_data(),
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

		return $items;
	}

	public function sanitize( array &$option, array $data ): void {
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

	private function get_default_post_metas(): array {
		return [
			'single'  => [
				'title'       => '{{ post.title }} {{ page }} {{ sep }} {{ site.title }}',
				'description' => '{{ post.auto_description }}',
			],
			'archive' => [
				'title'       => '{{ post_type.labels.plural }} {{ page }} {{ sep }} {{ site.title }}',
				'description' => '',
			],
		];
	}

	private function get_default_term_metas(): array {
		return [
			'title'       => '{{ term.title }} {{ page }} {{ sep }} {{ site.title }}',
			'description' => '{{ term.auto_description }}',
		];
	}

	private function get_default_author_metas(): array {
		return [
			'title'       => '{{ author.display_name }} {{ page }} {{ sep }} {{ site.title }}',
			'description' => '{{ author.description }}',
		];
	}
}
