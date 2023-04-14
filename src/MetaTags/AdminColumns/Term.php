<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Helper;

class Term extends Base {
	protected $object_type = 'term';

	public function setup_admin() {
		parent::setup_admin();

		foreach ( $this->types as $type ) {
			add_filter( "manage_edit-{$type}_columns", [ $this, 'columns' ] );
			add_filter( "manage_{$type}_custom_column", [ $this, 'render' ], 10, 3 );
		}
	}

	/**
	 * Render the column.
	 * The value of meta tags will be applied with filters to make them work in the back end.
	 */
	public function render( $output, $column, $term_id ) {
		switch ( $column ) {
			case 'meta_title':
				$title = $this->title->get_term_value( $term_id );
				$title = apply_filters( 'slim_seo_meta_title', $title, $term_id );
				$title = Helper::normalize( $title );
				return $title;
			case 'meta_description':
				$data        = get_term_meta( $term_id, 'slim_seo', true ) ?: [];
				$description = $data['description'] ?? '';
				$description = apply_filters( 'slim_seo_meta_description', $description, $term_id );
				$description = Helper::normalize( $description );
				return $description;
			case 'index':
				$noindex = $this->robots->get_term_value( $term_id );
				$index   = apply_filters( 'slim_seo_robots_index', ! $noindex, $term_id );
				return $index ? '<span class="ss-success"></span>' : '<span class="ss-danger"></span>';
		}

		return $output;
	}

	protected function is_screen(): bool {
		$screen = get_current_screen();
		return $screen->base === 'edit-tags' && in_array( $screen->taxonomy, $this->types, true );
	}
}
