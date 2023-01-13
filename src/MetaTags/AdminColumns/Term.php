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
				/**
				 * Make the filter works in the back end as well.
				 * @see MetaTags/Title::filter_title()
				 */
				$title = apply_filters( 'slim_seo_meta_title', $title, $this->title );
				$title = Helper::normalize( $title );
				return $title;
			case 'meta_description':
				$data        = get_term_meta( $term_id, 'slim_seo', true ) ?: [];
				$description = $data['description'] ?? '';
				/**
				 * Make the filter works in the back end as well.
				 * @see MetaTags/Description::get_description()
				 */
				$description = apply_filters( 'slim_seo_meta_description', $description, $this->description );
				$description = Helper::normalize( $description );
				return $description;
			case 'index':
				$noindex = $this->robots->get_term_value( $term_id );
				/**
				 * Make the filter works in the back end as well.
				 * @see MetaTags/Robots::indexed()
				 */
				$index = apply_filters( 'slim_seo_robots_index', ! $noindex );
				return $index ? '<span class="ss-success"></span>' : '<span class="ss-danger"></span>';
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
