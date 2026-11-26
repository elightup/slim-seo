<?php
namespace SlimSEO\MetaTags\Data;

use SlimSEO\MetaTags\QueriedObject;
use SlimSEO\MetaTags\Helper;
use WP_Term;

class Term {
	private $term;

	public function __construct( int $term_id = 0 ) {
		$this->term = get_term( $term_id ?: QueriedObject::get_id() );
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

		$description = Helper::strip_all_shortcodes( (string) $this->term->description );
		$data        = [
			'name'             => Helper::strip_all_shortcodes( (string) $this->term->name ),
			'description'      => $description,
			'auto_description' => Helper::truncate( $description ),
		];

		return $data[ $name ] ?? '';
	}
}
