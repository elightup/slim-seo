<?php
namespace SlimSEO\Schema\Entities;

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

	public function add_child( $name, $entity ) {
		$this->children[ $name ] = [ '@id' => $entity->id ];
	}

	abstract public function get_schema();
}
