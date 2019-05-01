<?php
namespace SlimSEO\Schema;

class ImageObject extends Type {
	private $image_id;

	public function __construct( $image_id ) {
		$this->image_id = $image_id;
		$this->image    = get_post( $image_id );
	}

	public function get_schema() {
		if ( null === $this->image || ! get_attached_file( $this->image_id ) ) {
			return null;
		}

		$schema = [
			'@type'   => 'ImageObject',
			'@id'     => $this->get_id(),
			'caption' => $this->image->post_excerpt,
			'url'     => wp_get_attachment_url( $this->image_id ),
		];
		return $schema;
	}
}
