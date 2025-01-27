<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\Helpers\UI;

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
		$term_id = (int) $term_id;

		switch ( $column ) {
			case 'meta_title':
				$title = $this->title->get_rendered_term_value( $term_id );
				ob_start();
				UI::tooltip( $title, "<span class='ss-meta-content'>$title</span>", 'top' );
				return ob_get_clean();
			case 'meta_description':
				$description = $this->description->get_rendered_term_value( $term_id );
				ob_start();
				UI::tooltip( $description, "<span class='ss-meta-content'>$description</span>", 'top' );
				return ob_get_clean();
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
