<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\Helpers\UI;
use SlimSEO\MetaTags\Helper;

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
	public function render( $column, $post_id ) {
		switch ( $column ) {
			case 'meta_title':
				$title = $this->title->get_singular_value( $post_id );
				$title = apply_filters( 'slim_seo_meta_title', $title, $post_id );
				$title = Helper::normalize( $title );
				UI::tooltip( $title, "<span class='ss-meta-content'>$title</span>", 'top' );
				break;
			case 'meta_description':
				$data        = get_post_meta( $post_id, 'slim_seo', true ) ?: [];
				$description = $data['description'] ?? '';
				$description = apply_filters( 'slim_seo_meta_description', $description, $post_id );
				$description = Helper::normalize( $description );
				UI::tooltip( $description, "<span class='ss-meta-content'>$description</span>", 'top' );
				break;
			case 'index':
				$noindex = $this->robots->get_singular_value( $post_id );
				$index   = apply_filters( 'slim_seo_robots_index', ! $noindex, $post_id );
				echo $index ? '<span class="ss-success"></span>' : '<span class="ss-danger"></span>';
				break;
		}

		do_action( 'slim_seo_admin_column_render', $column, $post_id );
	}

	protected function is_screen(): bool {
		$screen = get_current_screen();
		return $screen->base === 'edit' && in_array( $screen->post_type, $this->types, true );
	}
}
