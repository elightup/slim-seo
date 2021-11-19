<?php
namespace SlimSEO\Schema\Types;

class SearchAction extends Base {
	public function generate() {
		return array(
			'@type'       => 'SearchAction',
			'@id'         => $this->id,
			'target'      => esc_url( home_url( '/' ) ) . '?s={search_term_string}',
			'query-input' => 'required name=search_term_string',
		);
	}
}
