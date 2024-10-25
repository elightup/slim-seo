<?php
namespace SlimSEO\Schema\Types;

use SlimSEO\Helpers\Images;

class ImageObject extends Base {
	private $image_id;
	private $image_url;
	public $image;

	public function is_active() {
		$result = parent::is_active();
		if ( ! $result ) {
			return $result;
		}

		if ( $this->image_id ) {
			$this->image = get_post( $this->image_id );
			return null !== $this->image && get_attached_file( $this->image_id );
		}

		return ! empty( $this->image_url );
	}

	public function set_image_id( int $id ): void {
		$this->image_id = $id;
	}

	public function set_image_url( string $url ): void {
		$id = Images::get_id_from_url( $url );
		if ( $id ) {
			$this->set_image_id( $id );
		} else {
			$this->image_url = $url;
		}
	}

	public function generate() {
		$schema = [
			'@type' => 'ImageObject',
			'@id'   => $this->id,
		];
		if ( $this->image_id ) {
			$info = wp_get_attachment_image_src( $this->image_id, 'full' );
			return array_merge( $schema, [
				'caption' => $this->image->post_excerpt,
				'url'     => $info[0],
				'width'   => $info[1],
				'height'  => $info[2],
			] );
		}

		if ( $this->image_url ) {
			return array_merge( $schema, [
				'url' => $this->image_url,
			] );
		}

		return $schema;
	}
}
