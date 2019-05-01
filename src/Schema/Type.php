<?php
namespace SlimSEO\Schema;

abstract class Type {
	public function __construct() {
		add_filter( 'slim_seo_schema_types', [ $this, 'add_schema' ] );
	}

	public function add_schema( $types ) {
		$type = $this->get_type();
		if ( false === apply_filters( "slim_seo_schema_{$type}_enable", true ) ) {
			return $types;
		}

		$schema = $this->get_schema();
		$schema = apply_filters( "slim_seo_schema_{$type}", $schema );
		if ( null === $schema ) {
			return $types;
		}

		$types[ $type ] = $schema;

		return $types;
	}

	private function get_type() {
		$class = get_class( $this );
		if ( $pos = strrpos( $class, '\\' ) ) {
			return substr( $class, $pos + 1 );
		}
		return $pos;
	}

	abstract protected function get_schema();
}
