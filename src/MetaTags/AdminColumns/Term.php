<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Helper;

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
				$title = $this->title->get_term_value( $term_id );
				return Helper::normalize( $title );
			case 'meta_description':
				$data = get_term_meta( $term_id, 'slim_seo', true );
				if ( ! empty( $data['description'] ) ) {
					return Helper::normalize( $data['description'] );
				}
				break;
		}

		return $output;
	}
}
