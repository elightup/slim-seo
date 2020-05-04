<?php
namespace SlimSEO\MetaTags;

class Image {
	use Context;

	private $meta_key;

	public function __construct( $meta_key ) {
		$this->meta_key = $meta_key;
	}

	private function get_home_value() {
		$data = get_option( 'slim_seo' );
		if ( empty( $data[ "home_{$this->meta_key}" ] ) ) {
			return null;
		}
		$image_id = attachment_url_to_postid( $data[ "home_{$this->meta_key}" ] );
		return $image_id ? wp_get_attachment_image_src( $image_id, 'full' ) : [ "home_{$this->meta_key}" ];
	}

	private function get_singular_value() {
		$data = get_post_meta( get_queried_object_id(), 'slim_seo', true );
		if ( isset( $data[ $this->meta_key ] ) ) {
			$image_id = attachment_url_to_postid( $data[ $this->meta_key ] );
			return $image_id ? wp_get_attachment_image_src( $image_id, 'full' ) : [ $data[ $this->meta_key ] ];
		}
		return has_post_thumbnail() ? wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ) : null;
	}

	private function get_term_value() {
		$data = get_term_meta( get_queried_object_id(), 'slim_seo', true );
		if ( empty( $data[ $this->meta_key ] ) ) {
			return null;
		}
		$image_id = attachment_url_to_postid( $data[ $this->meta_key ] );
		return $image_id ? wp_get_attachment_image_src( $image_id, 'full' ) : [ $data[ $this->meta_key ] ];
	}
}