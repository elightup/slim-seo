<?php
namespace SlimSEO\Integrations\MetaBox;

class Id {
	public static function normalize( $id ) {
		return str_replace( '-', '_', $id );
	}
}
