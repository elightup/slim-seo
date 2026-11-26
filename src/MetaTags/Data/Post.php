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

		$data   = [
			'date'          => wp_date( get_option( 'date_format' ), strtotime( $this->post->post_date_gmt ) ),
			'modified_date' => wp_date( get_option( 'date_format' ), strtotime( $this->post->post_modified_gmt ) ),
		];
		$method = "get_$name";

		return $data[ $name ] ?? ( method_exists( $this, $method ) ? $this->$method() : '' );
	}

	private function get_title(): string {
		return Helper::sanitize_post_shortcodes( (string) $this->post->post_title, $this->post );
	}

	private function get_excerpt(): string {
		return Helper::sanitize_post_shortcodes( (string) $this->post->post_excerpt, $this->post );
	}

	private function get_content(): string {
		return Data::get_post_content( $this->post->ID );
	}

	private function get_auto_description(): string {
		return Helper::truncate( $this->get_excerpt() ?: $this->get_content() );
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
		$allow       = Helper::can_execute_shortcodes( $this->post );
		$data        = [];
		foreach ( $meta_values as $key => $value ) {
			// Plugins like JetEngine can hook to "get_{$object_type}_metadata" to add its data from custom table
			// which might not follow WordPress standards of auto serialization/unserialization for arrays
			// so we will add a check to bypass invalid values here.
			$value        = is_array( $value ) ? reset( $value ) : '';
			$data[ $key ] = $allow ? $value : map_deep( $value, static function ( $item ) {
				return is_string( $item ) ? Helper::strip_shortcode_syntax( $item ) : $item;
			} );
		}
		return $data;
	}

	private function get_tax(): array {
		$post_tax = [];

		/**
		 * Get all taxonomies for meta tags.
		 * Don't use eLightUp\SlimSEO\Common\Helpers\Data::get_taxonomies() because it doesn't include non-public taxonomies like WooCommerce product attributes.
		 */
		$taxonomies = Helper::get_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			$post_tax[ $this->normalize( $taxonomy['slug'] ) ] = $this->get_post_terms( $taxonomy['slug'] );
		}

		return $post_tax;
	}

	private function get_post_terms( string $taxonomy ): array {
		/**
		 * Allow to short-circuit getting the terms for the post's taxonomy.
		 * @see \SlimSEO\Integrations\WooCommerce::change_post_terms()
		 */
		$terms = apply_filters( 'slim_seo_meta_tags_post_terms', [], $this->post->ID, $taxonomy );
		if ( empty( $terms ) ) {
			$terms = get_the_terms( $this->post, $taxonomy );
			$terms = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : [];
		}

		return array_map(
			static function ( $term ): string {
				return Helper::strip_all_shortcodes( (string) $term );
			},
			(array) $terms
		);
	}

	private function normalize( string $key ): string {
		return str_replace( '-', '_', $key );
	}
}
