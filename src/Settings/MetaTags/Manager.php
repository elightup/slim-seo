<?php
namespace SlimSEO\Settings\MetaTags;

use SlimSEO\Helpers\Data;

class Manager {
	private $items = [];

	public function setup() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_init() {
		$items = array_keys( array_filter( $this->get_post_types(), function ( $post_type_object ) {
			return $post_type_object->has_archive;
		} ) );
		$items = array_map( function ( $item ) {
			return "{$item}_archive";
		}, $items );

		if ( $this->has_homepage_settings() ) {
			$items[] = 'home';
		}

		foreach ( $items as $item ) {
			$this->items[ $item ] = new Item( $item );
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
		wp_enqueue_script( 'slim-seo-meta-tags', SLIM_SEO_URL . 'js/meta-tags/dist/settings.js', [ 'jquery', 'underscore' ], filemtime( SLIM_SEO_DIR . '/js/meta-tags/dist/settings.js' ), true );
		wp_localize_script( 'slim-seo-meta-tags', 'ss', [
			'mediaPopupTitle' => __( 'Select An Image', 'slim-seo' ),
			'items'           => array_keys( $this->items ),
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
}
