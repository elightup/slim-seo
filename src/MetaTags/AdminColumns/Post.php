<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Helper;

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
				$title = $this->title->get_singular_value( $post_id );
				echo esc_html( Helper::normalize( $title ) );
				break;
			case 'meta_description':
				$data = get_post_meta( $post_id, 'slim_seo', true );
				if ( ! empty( $data['description'] ) ) {
					echo esc_html( Helper::normalize( $data['description'] ) );
				}
				break;
		}
	}
}
