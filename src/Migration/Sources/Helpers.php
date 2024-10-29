<?php
namespace SlimSEO\Migration\Sources;

class Helpers {
	public static function parse_variables( string $text ): array {
		$pattern = '/%%([^%%]+)%%/';
		preg_match_all($pattern, $text, $matches);

		return $matches;
	}
}