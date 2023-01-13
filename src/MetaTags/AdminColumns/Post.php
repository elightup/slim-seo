<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Helper;

class Post extends Base {
	protected $object_type = 'post';

	public function setup_admin() {
		parent::setup_admin();

		foreach ( $this->types as $type ) {
			add_filter( "manage_{$type}_posts_columns", [ $this, 'columns' ] );
			add_action( "manage_{$type}_posts_custom_column", [ $this, 'render' ], 10, 2 );
		}

		add_action( 'admin_print_styles-edit.php', [ $this, 'enqueue' ] );
	}

	public function render( $column, $post_id ) {
		switch ( $column ) {
			case 'meta_title':
				$title = $this->title->get_singular_value( $post_id );
				/**
				 * Make the filter works in the back end as well.
				 * @see MetaTags/Title::filter_title()
				 */
				$title = apply_filters( 'slim_seo_meta_title', $title, $this->title );
				$title = Helper::normalize( $title );
				echo esc_html( $title );
				break;
			case 'meta_description':
				$data        = get_post_meta( $post_id, 'slim_seo', true ) ?: [];
				$description = $data['description'] ?? '';
				/**
				 * Make the filter works in the back end as well.
				 * @see MetaTags/Description::get_description()
				 */
				$description = apply_filters( 'slim_seo_meta_description', $description, $this->description );
				$description = Helper::normalize( $description );
				echo esc_html( $description );
				break;
			case 'index':
				$noindex = $this->robots->get_singular_value( $post_id );
				/**
				 * Make the filter works in the back end as well.
				 * @see MetaTags/Robots::indexed()
				 */
				$index = apply_filters( 'slim_seo_robots_index', ! $noindex );
				echo $index ? '<span class="ss-success"></span>' : '<span class="ss-danger"></span>';
				break;
		}
	}

	public function enqueue() {
		if ( ! $this->is_screen() ) {
			return;
		}
		wp_enqueue_style( 'slim-seo-edit', SLIM_SEO_URL . 'css/edit.css', [], SLIM_SEO_VER );
		wp_enqueue_script( 'slim-seo-bulk', SLIM_SEO_URL . 'js/bulk.js', [], SLIM_SEO_VER, true );
	}

	protected function is_screen(): bool {
		$screen = get_current_screen();
		return $screen->base === 'edit' && in_array( $screen->post_type, $this->types, true );
	}
}
