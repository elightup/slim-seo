<?php
namespace SlimSEO\MetaTags;

class QueriedObject {
	private static $object = null;
	private static $id = 0;

	public static function set( $object ): void {
		self::$object = $object;
	}

	public static function get() {
		return self::$object ?: get_queried_object();
	}

	public static function set_id( int $id ): void {
		self::$id = $id;
	}

	public static function get_id(): int {
		return self::$id ?: get_queried_object_id();
	}
}
