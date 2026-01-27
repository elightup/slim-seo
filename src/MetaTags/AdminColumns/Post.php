<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\Helpers\UI;

class Post extends Base {
	protected $object_type = 'post';

	public function setup_admin() {
		parent::setup_admin();

		foreach ( $this->types as $type ) {
			add_filter( "manage_{$type}_posts_columns", [ $this, 'columns' ] );
			add_action( "manage_{$type}_posts_custom_column", [ $this, 'render' ], 10, 2 );
		}
	}

	/**
	 * Render the column.
	 * The value of meta tags will be applied with filters to make them work in the back end.
	 */
	public function render( $column, $post_id ): void {
		$post_id       = (int) $post_id;
		$custom_output = apply_filters( 'slim_seo_custom_column_output', '', $column, $post_id, 'post' );

		if ( $custom_output ) {
			echo $custom_output; // phpcs:ignore

			return;
		}

		switch ( $column ) {
			case 'meta_title':
				$title = $this->title->get_rendered_singular_value( $post_id );
				if ( $this->title->check_is_manual() ) {
					UI::tooltip( __( 'Manual title', 'slim-seo' ), $this->manual_indicator, 'top' );
				}
				UI::tooltip( $title, "<span class='ss-meta-content'>$title</span>", 'top' );
				break;
			case 'meta_description':
				$description = $this->description->get_rendered_singular_value( $post_id );
				if ( $this->description->check_is_manual() ) {
					UI::tooltip( __( 'Manual description', 'slim-seo' ), $this->manual_indicator, 'top' );
				}
				UI::tooltip( $description, "<span class='ss-meta-content'>$description</span>", 'top' );
				break;
			case 'index':
				$noindex = $this->robots->get_singular_value( $post_id );
				$index   = apply_filters( 'slim_seo_robots_index', ! $noindex, $post_id );
				echo $index ? '<span class="ss-success"></span>' : '<span class="ss-danger"></span>';
				break;
		}
	}

	protected function is_screen(): bool {
		$screen = get_current_screen();
		return $screen->base === 'edit' && in_array( $screen->post_type, $this->types, true );
	}
}
