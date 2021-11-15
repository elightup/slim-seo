<?php
namespace SlimSEO\MetaTags\AdminColumns;

class Post extends Base {
	public function setup() {
		$types = $this->settings->get_types();

		foreach ( $types as $type ) {
			add_filter( "manage_{$type}_posts_columns", [ $this, 'columns' ] );
			add_action( "manage_{$type}_posts_custom_column", [ $this, 'render' ], 10, 2 );
		}
	}

	public function render( $column, $post_id ) {
		switch ( $column ) {
			case 'meta_title':
				echo esc_html( $this->title->get_singular_value( $post_id ) );
				break;
			case 'meta_description':
				echo esc_html( $this->description->get_singular_value( $post_id ) );
				break;
		}
	}
}