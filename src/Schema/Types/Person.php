<?php
namespace SlimSEO\Schema\Types;

class Person extends Base {
	protected $user;

	public function generate() {
		$schema = [
			'@type'      => 'Person',
			'@id'        => $this->id,
			'name'       => $this->user->display_name,
			'givenName'  => $this->user->first_name,
			'familyName' => $this->user->last_name,
		];

		return $schema;
	}
}
