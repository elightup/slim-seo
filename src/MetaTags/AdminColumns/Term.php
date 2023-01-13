<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Helper;

class Term extends Base {
	private $types;

	public function setup() {
		$this->types = $this->settings->get_types();

		foreach ( $this->types as $type ) {
			add_filter( "manage_edit-{$type}_columns", [ $this, 'columns' ] );
			add_filter( "manage_{$type}_custom_column", [ $this, 'render' ], 10, 3 );
		}

		add_action( 'admin_print_styles-edit-tags.php', [ $this, 'enqueue' ] );
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
			case 'noindex':
				$noindex = $this->robots->get_term_value( $term_id );
				/**
				 * Make the filter works in the back end as well.
				 * @see MetaTags/Robots::indexed()
				 */
				$index = apply_filters( 'slim_seo_robots_index', ! $noindex );
				echo $index ? '<span class="ss-success"></span>' : '<span class="ss-danger"></span>';
				break;
		}

		return $output;
	}

	public function enqueue() {
		if ( ! in_array( get_current_screen()->taxonomy, $this->types, true ) ) {
			return;
		}
		wp_enqueue_style( 'slim-seo-edit', SLIM_SEO_URL . 'css/edit.css', [], SLIM_SEO_VER );
	}
}
