<?php
namespace SlimSEO\Schema;

abstract class Type {
	public function __construct() {
		add_action( 'slim_seo_schema', [ $this, 'add_schema' ] );
	}

	public function add_schema( $data ) {
		$type = $this->get_type();
		if ( false === apply_filters( "slim_seo_schema_{$type}_enable", true ) ) {
			return $data;
		}

		$schema = $this->get_schema();
		$schema = apply_filters( "slim_seo_schema_{$type}", $schema );
		if ( null === $schema ) {
			return $data;
		}

		$data[ $type ] = $schema;

		return $data;
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
