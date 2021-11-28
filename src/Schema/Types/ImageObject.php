<?php
namespace SlimSEO\Schema\Types;

class ImageObject extends Base {
	public $image_id;
	public $image;

	public function is_active() {
		$result = parent::is_active();
		if ( ! $this->image_id || ! $result ) {
			return $result;
		}

		$this->image = get_post( $this->image_id );
		return null !== $this->image && get_attached_file( $this->image_id );
	}

	public function generate() {
		$schema = [
			'@type' => 'ImageObject',
			'@id'   => $this->id,
		];
		if ( $this->image_id ) {
			$info   = wp_get_attachment_image_src( $this->image_id, 'full' );
			$schema = array_merge( $schema, [
				'caption' => $this->image->post_excerpt,
				'url'     => $info[0],
				'width'   => $info[1],
				'height'  => $info[2],
			] );
		}

		return $schema;
	}
}
