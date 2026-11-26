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

		$method = "get_$name";

		return method_exists( $this, $method ) ? $this->$method() : '';
	}

	private function get_name(): string {
		return Helper::strip_all_shortcodes( (string) $this->term->name );
	}

	private function get_description(): string {
		return Helper::strip_all_shortcodes( (string) $this->term->description );
	}

	private function get_auto_description(): string {
		return Helper::truncate( $this->get_description() );
	}
}
