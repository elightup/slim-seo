<?php
namespace SlimSEO\Schema\Types;

class Organization extends Base {
	public function generate() {
		$schema = [
			'@type' => 'Organization',
			'@id'   => $this->id,
			'url'   => $this->url,
			'name'  => get_bloginfo( 'name' ),
		];
		return array_filter( $schema );
	}
}
