<?php
namespace SlimSEO\MetaTags;

class BulkEdit {

	public function __construct() {
		
	}
	public function setup() {
		add_filter( 'manage_post_posts_columns', [ $this, 'add_index_columns' ] );
		add_action( 'manage_posts_custom_column', [ $this, 'populate_columns' ], 10, 2 );
		add_filter( 'manage_post_pages_columns', [ $this, 'add_index_columns' ] );
		add_action( 'manage_pages_custom_column', [ $this, 'populate_columns' ], 10, 2 );
		add_action( 'bulk_edit_custom_box', [ $this, 'quick_edit_fields' ], 10, 2 );

		add_action( 'admin_bulk_edit-slim-seo', [ $this, 'enqueue' ], 1 );
	}

	function add_index_columns( $column_array ) {
		$column_array['index'] = 'Index';
		return $column_array;
	}
	function populate_columns( $column_name, $id ) {
		switch( $column_name ) :
			case 'meta_title':
				echo get_post_meta( $id, 'slimseo_post_meta_title', true );
				break;
			case 'meta_description':
				echo get_post_meta( $id, 'slimseo_post_meta_description', true );
				break;
			case 'index':
				if( get_post_meta( $id,'slimseo_post_index',true ) == true ) 
					echo 'Yes';
				break;
		endswitch;

	}
	public function quick_edit_fields( $column_name, $post_type  ) {
		switch( $column_name ) :
			case 'meta_title': {
				wp_nonce_field( 'slimseo_bulk_edit_nonce', 'misha_nonce' );

				echo '<fieldset class="inline-edit-col-right">
					<div class="inline-edit-col">
						<div class="inline-edit-group wp-clearfix">';

				echo '<label class="alignleft">
						<span class="title">Meta title</span>
						<span class="input-text-wrap"><input type="text" name="slimseo_post_meta_title" value=""></span>
					</label></div>';

				break;

			}
			case 'meta_description': {
				wp_nonce_field( 'slimseo_bulk_edit_nonce', 'misha_nonce' );

				echo '<div class="inline-edit-group wp-clearfix">';

				echo '<label class="alignleft">
						<span class="title">Meta description</span>
						<span class="input-text-wrap"><input type="text" name="slimseo_post_meta_description" value=""></span>
					</label></div>';

				break;

			}
			case 'index': {

				echo '<label class="alignleft">
						<input type="checkbox" name="featured">
						<span class="checkbox-title">Index</span>
					</label>';

				// for the last column only - closing the fieldset element
				echo '</div></fieldset>';

				break;

			}

		endswitch;
	}
	public function enqueue() {
		if ( 'edit.php' != $pagehook ) {
			return;
		}

		wp_enqueue_script( 'slim-seo-populate', SLIM_SEO_URL . 'js/populate.js', [], SLIM_SEO_VER, true );

	}
}