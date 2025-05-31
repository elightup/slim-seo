<?php
namespace SlimSEO\MetaTags\Data;

use SlimSEO\MetaTags\QueriedObject;
use SlimSEO\MetaTags\Helper;
use SlimSEO\MetaTags\Data;

class Term {
	private $term;
	private $live_data;

	public function __construct( int $term_id = 0, array $live_data = [] ) {
		$this->term      = get_term( $term_id ) ?: QueriedObject::get();
		$this->live_data = $live_data;
	}

	/**
	 * Must return true to make __get works.
	 */
	public function __isset( string $name ): bool {
		return true;
	}

	public function __get( string $name ) {
		if ( ! $this->term ) {
			return '';
		}

		$data  = [
			'name'    => $this->live_data['term']['name'] ?? $this->term->name,
		];
		$method = "get_$name";

		return $data[ $name ] ?? $this->$method();
	}

	private function get_description(): string {
		return $this->live_data['term']['description'] ?? $this->term->description;
	}

	private function get_auto_description(): string {
		return $this->live_data['term']['auto_description'] ?? Helper::truncate( $this->term->description );
	}
}