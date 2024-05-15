<?php
namespace SlimSEO\MetaTags;

use SlimSEO\Helpers\Images;

class Image {
	use Context;

	private $meta_key;

	public function __construct( $meta_key ) {
		$this->meta_key = $meta_key;
	}

	private function get_home_value(): array {
		$option = get_option( 'slim_seo', [] );
		$url    = $option['home'][ $this->meta_key ] ?? '';
		return $url ? $this->get_data_from_url( $url ) : [];
	}

	private function get_post_type_archive_value(): array {
		$post_type_object = get_queried_object();
		$option           = get_option( 'slim_seo' );
		$url              = $option[ "{$post_type_object->name}_archive" ][ $this->meta_key ] ?? '';
		return $url ? $this->get_data_from_url( $url ) : [];
	}

	private function get_singular_value(): array {
		// Get from SEO settings in custom fields.
		$data = get_post_meta( $this->get_queried_object_id(), 'slim_seo', true );
		if ( isset( $data[ $this->meta_key ] ) ) {
			return $this->get_data_from_url( $data[ $this->meta_key ] );
		}

		// Get from thumbnail or content.
		$images = Images::get_post_images( $this->get_queried_object() );
		if ( empty( $images ) ) {
			return [];
		}

		$first_image = reset( $images );
		$method      = is_numeric( $first_image ) ? 'get_data' : 'get_data_from_url';
		return $this->$method( $first_image );
	}

	private function get_term_value(): array {
		$data = get_term_meta( get_queried_object_id(), 'slim_seo', true );
		return isset( $data[ $this->meta_key ] ) ? $this->get_data_from_url( $data[ $this->meta_key ] ) : [];
	}

	public function get_data_from_url( $url ): array {
		$id = Images::get_id_from_url( $url );
		return $id ? $this->get_data( $id ) : [
			'src' => $url,
		];
	}

	private function get_data( $id ): array {
		$image = wp_get_attachment_image_src( $id, 'full' );
		return $image ? [
			'id'     => $id,
			'src'    => $image[0],
			'width'  => $image[1],
			'height' => $image[2],
			'alt'    => get_post_meta( $id, '_wp_attachment_image_alt', true ) ?: get_the_title( $id ),
		] : [];
	}
}
