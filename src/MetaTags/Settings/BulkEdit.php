<?php
namespace SlimSEO\MetaTags\Settings;

class BulkEdit extends Base {

	public function __construct() {
		
	}
	public function setup() {
		$this->object_type = 'post';
		add_filter( 'manage_posts_columns', [ $this, 'add_extra_columns' ] );
		add_filter( 'manage_pages_columns', [ $this, 'add_extra_columns' ] );

		add_action( 'manage_posts_custom_column', [ $this, 'populate_columns' ], 10, 2 );
		add_action( 'manage_pages_custom_column', [ $this, 'populate_columns' ], 10, 2 );

		add_action( 'quick_edit_custom_box', [ $this, 'edit_fields' ], 10, 2 );
		add_action( 'bulk_edit_custom_box', [ $this, 'edit_fields' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );

		add_action( 'save_post', [ $this, 'save' ] );
	}

	function add_extra_columns( $column_array ) {
		$column_array['slim_seo[title]'] = 'Meta title';
		$column_array['slim_seo[description]'] = 'Meta description';
		$column_array['slim_seo[noindex]'] = 'Index';
		return $column_array;
	}
	function populate_columns( $column_name, $id ) {
		$data = $this->get_data();
		switch( $column_name ) :
			case 'slim_seo[title]':
				echo esc_attr( $data['title'] );
				break;
			case 'slim_seo[description]':
				echo esc_attr( $data['description'] );
				break;
			case 'slim_seo[noindex]':
				echo ( $data['noindex'] == true ) ? 'Yes' : 'No';
				break;
		endswitch;

	}
	public function edit_fields( $column_name, $post_type  ) {
		switch( $column_name ) :
			case 'slim_seo[title]':
				wp_nonce_field( 'save', 'ss_nonce' );

				echo '<fieldset class="inline-edit-col-right">
					<div class="inline-edit-col">
						<div class="inline-edit-group wp-clearfix">';
				echo '<label class="alignleft">
						<span class="title">Meta title</span>
						<span class="input-text-wrap"><input type="text" name="slim_seo[title]" value=""></span>
					</label></div>';
				break;
			case 'slim_seo[description]': 
				echo '<div class="inline-edit-group wp-clearfix">';
				echo '<label class="alignleft">
						<span class="title">Meta description</span>
						<span class="input-text-wrap"><input type="text" name="slim_seo[description]" value=""></span>
					</label></div>';
				break;
			case 'slim_seo[noindex]': 
				echo '<label class="alignleft">
						<input type="checkbox" name="slim_seo[noindex]">
						<span class="checkbox-title">Hide from search results</span>
					</label>';
				// Closing the fieldset element on last element
				echo '</div></fieldset>';
				break;
		endswitch;
	}
	public function enqueue( ) {
		wp_enqueue_script( 'slim-seo-populate', SLIM_SEO_URL . 'js/populate.js', [], SLIM_SEO_VER, true );
	}
	public function get_types() {}

	protected function get_object_id() {
		return get_the_ID();
	}
}