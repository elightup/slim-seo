<?php
namespace SlimSEO\Integrations\MetaBox;

class Id {
	public static function normalize( string $id ): string {
		return str_replace( '-', '_', $id );
	}
}
