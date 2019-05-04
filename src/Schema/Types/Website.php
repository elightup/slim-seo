<?php
namespace SlimSEO\Schema\Types;

class Website extends Base {
	public function generate_schema() {
		$schema = [
			'@type'    => 'WebSite',
			'@id'      => $this->id,
			'url'      => $this->url,
			'name'     => get_bloginfo( 'name' ),
		];

		return $schema;
	}
}
