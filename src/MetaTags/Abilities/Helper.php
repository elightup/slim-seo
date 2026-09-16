<?php
namespace SlimSEO\MetaTags\Abilities;

use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Description;

class Helper {
	public static function get_default_data( string $object_type ): array {
		return [
			'title'       => Title::DEFAULTS[ $object_type ] ?? '',
			'description' => Description::DEFAULTS[ $object_type ] ?? '',
		];
	}
}
