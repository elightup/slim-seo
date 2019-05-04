<?php
namespace SlimSEO\Schema\Types;

class ImageObject extends Base {
	protected $image_id;
	protected $image;

	public function is_active() {
		if ( ! parent::is_active() ) {
			return false;
		}

		$this->image = get_post( $this->image_id );
		return null !== $this->image && get_attached_file( $this->image_id );
	}

	public function generate_schema() {
		$schema = [
			'@type'   => 'ImageObject',
			'@id'     => $this->id,
			'caption' => $this->image->post_excerpt,
			'url'     => wp_get_attachment_url( $this->image_id ),
		];
		return $schema;
	}
}
