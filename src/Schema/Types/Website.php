<?php
namespace SlimSEO\Schema\Types;

class Website extends Base {
	public function generate() {
		$schema = [
			'@type'       => 'WebSite',
			'@id'         => $this->id,
			'url'         => $this->url,
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'inLanguage'  => get_bloginfo( 'language' )
		];

		return $schema;
	}
}
