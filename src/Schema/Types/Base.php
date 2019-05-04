<?php
namespace SlimSEO\Schema\Types;

abstract class Base {
	protected $url;
	protected $context;
	protected $id;
	protected $parent;
	protected $children = [];

	public function __construct( $url = null, $context = null ) {
		$this->url     = $url ?: $this->get_current_url();
		$this->context = $context ?: $this->get_type();
		$this->id      = $this->url . '#' . $this->context;
	}

	public function __set( $name, $value ) {
		$this->$name = $value;
	}

	public function __get( $name ) {
		return $this->$name;
	}

	public function is_active() {
		return apply_filters( "slim_seo_schema_{$this->context}_enable", true );
	}

	public function get_current_url() {
		global $wp;

		$url = add_query_arg( [], $wp->request );
		$url = home_url( $url );
		$url = strtok( $url, '#' );
		$url = strtok( $url, '?' );

		return $url;
	}

	public function get_type() {
		$class = get_class( $this );
		$pos   = strrpos( $class, '\\' );
		$type  = substr( $class, $pos + 1 );

		return strtolower( $type );
	}

	public function set_parent( $entity ) {
		if ( $entity->is_active() ) {
			$this->parent = $entity;
		}
	}

	public function add_child( $name, $entity ) {
		if ( $entity->is_active() ) {
			$this->children[ $name ] = $entity;
		}
	}

	public function get_schema() {
		$schema = $this->generate_schema();

		if ( null !== $this->parent ) {
			$schema['isPartOf'] = [ '@id' => $this->parent->id ];
		}
		if ( ! empty( $this->children ) ) {
			foreach ( $this->children as $name => $entity ) {
				$schema[ $name ] = [ '@id' => $entity->id ];
			}
		}

		return $schema;
	}

	abstract function generate_schema();
}
