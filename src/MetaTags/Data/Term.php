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

		$data = [
			// Low-privileged users can create terms (like bbPress topic tags). Remove square brackets, so that Helper::normalize() cannot run shortcodes.
			'name'             => str_replace( [ '[', ']' ], '', $this->term->name ),
			'description'      => $this->term->description,
			'auto_description' => Helper::truncate( $this->term->description, 160, Helper::can_run_shortcodes( 0, $this->term->term_id ) ),
		];

		return $data[ $name ] ?? '';
	}
}
