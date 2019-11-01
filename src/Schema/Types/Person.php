<?php
namespace SlimSEO\Schema\Types;

class Person extends Base {
	public $user;

	public function generate() {
		$schema = [
			'@type'       => 'Person',
			'@id'         => $this->id,
			'url'         => get_author_posts_url( $this->user->ID ),
			'name'        => $this->user->display_name,
			'description' => $this->user->description,
			'givenName'   => $this->user->first_name,
			'familyName'  => $this->user->last_name,
		];

		return $schema;
	}
}
