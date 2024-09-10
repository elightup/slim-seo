<?php
namespace SlimSEO\Schema\Types;

use WP_User;

class Person extends Base {
	private $user;

	public function set_user( WP_User $user ) {
		$this->user = $user;
		$this->id   = home_url( '/#/schema/person/' . md5( $user->user_login ) );
	}

	public function generate() {
		$schema = [
			'@type'       => 'Person',
			'@id'         => $this->id,
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
