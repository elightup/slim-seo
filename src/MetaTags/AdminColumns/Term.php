<?php
namespace SlimSEO\MetaTags\AdminColumns;

class Term extends Base {
	public function setup() {
		$types = $this->settings->get_types();

		foreach ( $types as $type ) {
			add_filter( "manage_edit-{$type}_columns", array( $this, 'columns' ) );
			add_filter( "manage_{$type}_custom_column", array( $this, 'render' ), 10, 3 );
		}
	}

	public function render( $output, $column, $term_id ) {
		switch ( $column ) {
			case 'meta_title':
				return $this->title->get_term_value( $term_id );
				break;
			case 'meta_description':
				return $this->description->get_term_value( $term_id );
				break;
		}

		return $output;
	}
}