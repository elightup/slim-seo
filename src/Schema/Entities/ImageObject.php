<?php
namespace SlimSEO\Schema\Entities;

class ImageObject extends Base {
	protected $image_id;

	public function get_schema() {
		$this->image = get_post( $this->image_id );
		if ( null === $this->image || ! get_attached_file( $this->image_id ) ) {
			return null;
		}

		$schema = [
			'@type'   => 'ImageObject',
			'@id'     => $this->id,
			'caption' => $this->image->post_excerpt,
			'url'     => wp_get_attachment_url( $this->image_id ),
		];
		return $schema;
	}
}
