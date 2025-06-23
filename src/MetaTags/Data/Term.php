<?php
namespace SlimSEO\MetaTags\Data;

use SlimSEO\MetaTags\QueriedObject;
use SlimSEO\MetaTags\Helper;
use WP_Term;

class Term {
	private $term;
	private $data;

	public function __construct( int $term_id = 0, array $data = [] ) {
		$this->term = get_term( $term_id ?: QueriedObject::get_id() );
		$this->data = $data;
	}

	/**
	 * Must return true to make __get works.
	 */
	public function __isset( string $name ): bool {
		return true;
	}

	public function __get( string $name ) {
		if ( ! ( $this->term instanceof WP_Term ) ) {
			return '';
		}

		$data  =  [
			'name'             => $this->data['name'] ?? $this->term->name,
			'description'      => $this->data['description'] ?? $this->term->description,
			'auto_description' => $this->data['auto_description'] ?? Helper::truncate( $this->term->description ),
		];

		return $data[ $name ] ?? '';
	}
}