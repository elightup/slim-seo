<?php
namespace SlimSEO\Schema\Types;

class Person extends Base {
	public $user;

	public function generate() {
		$schema = [
			'@type'       => 'Person',
			'@id'         => home_url( '/#/schema/person/' . md5( $this->user->user_login ) ),
			'url'         => $this->user->user_url,
			'name'        => $this->user->display_name,
			'description' => $this->user->description,
			'givenName'   => $this->user->first_name,
			'familyName'  => $this->user->last_name,
			'image'       => get_avatar_url( $this->user->ID ),
		];

		return $schema;
	}
}
