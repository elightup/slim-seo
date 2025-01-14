<?php
namespace SlimSEO\Schema\Types;

class About extends Base {
	public function generate() {
		return [
			'@type'       => 'Thing',
			'@id'         => $this->id,
			'target'      => esc_url( home_url( '/' ) ) . '#organization',
		];
	}
}
