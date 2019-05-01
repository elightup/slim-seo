<?php
namespace SlimSEO\Schema;

abstract class Type {
	public function register() {
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
		$pos   = strrpos( $class, '\\' );
		$type  = substr( $class, $pos + 1 );

		return strtolower( $type );
	}

	public function get_url( $global = false ) {
		global $wp;

		$url = $global ? '/' : add_query_arg( [], $wp->request );
		$url = home_url( $url );
		$url = strtok( $url, '#' );
		$url = strtok( $url, '?' );

		return esc_url( $url );
	}

	public function get_id( $type = null, $global = false ) {
		$url  = $this->get_url( $global );
		$type = $type ?: $this->get_type();

		return $url . '#' . $type;
	}

	abstract public function get_schema();
}
