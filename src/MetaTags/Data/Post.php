<?php
namespace SlimSEO\MetaTags\Data;

use SlimSEO\MetaTags\QueriedObject;
use SlimSEO\MetaTags\Helper;
use SlimSEO\MetaTags\Data;

class Post {
	private $post;

	public function __construct( int $post_id = 0 ) {
		$this->post = get_post( $post_id ?: QueriedObject::get_id() );
	}

	/**
	 * Must return true to make __get works.
	 */
	public function __isset( string $name ): bool {
		return true;
	}

	public function __get( string $name ) {
		if ( empty( $this->post ) ) {
			return '';
		}

		$data  = [
			'title'         => $this->post->post_title,
			'excerpt'       => $this->post->post_excerpt,
			'date'          => wp_date( get_option( 'date_format' ), strtotime( $this->post->post_date_gmt ) ),
			'modified_date' => wp_date( get_option( 'date_format' ), strtotime( $this->post->post_modified_gmt ) ),
		];
		$method = "get_$name";

		return $data[ $name ] ?? ( method_exists( $this, $method ) ? $this->$method() : '' );
	}

	private function get_content(): string {
		return Data::get_post_content( $this->post->ID );
	}

	private function get_auto_description(): string {
		return Helper::truncate( $this->post->post_excerpt  ?: $this->get_content() );
	}

	private function get_thumbnail() {
		return get_the_post_thumbnail_url( $this->post->ID, 'full' );
	}

	private function get_tags(): array {
		return $this->get_post_terms( 'post_tag' );
	}

	private function get_categories(): array {
		return $this->get_post_terms( 'category' );
	}

	private function get_custom_field(): array {
		$meta_values = get_post_meta( $this->post->ID ) ?: [];
		$data        = [];
		foreach ( $meta_values as $key => $value ) {
			// Plugins like JetEngine can hook to "get_{$object_type}_metadata" to add its data from custom table
			// which might not follow WordPress standards of auto serialization/unserialization for arrays
			// so we will add a check to bypass invalid values here.
			$data[ $key ] = is_array( $value ) ? reset( $value ) : '';
		}
		return $data;
	}

	private function get_tax(): array {
		$post_tax   = [];
		$taxonomies = Helper::get_taxonomies();
		unset( $taxonomies['category'], $taxonomies['post_tag'] );
		foreach ( $taxonomies as $taxonomy ) {
			$post_tax[ $this->normalize( $taxonomy['slug'] ) ] = $this->get_post_terms( $taxonomy['slug'] );
		}

		return $post_tax;
	}

	private function get_post_terms( string $taxonomy ): array {
		$terms = get_the_terms( $this->post, $taxonomy );
		return is_wp_error( $terms ) ? [] : wp_list_pluck( $terms, 'name' );
	}

	private function normalize( string $key ): string {
		return str_replace( '-', '_', $key );
	}
}