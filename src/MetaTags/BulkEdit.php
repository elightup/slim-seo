<?php
namespace SlimSEO\MetaTags;

class BulkEdit {
	protected $defaults = [
		'title'          => '',
		'description'    => '',
		'noindex'        => 0,
	];

	public function setup() {
		$this->object_type = 'post';
		add_filter( 'manage_posts_columns', [ $this, 'add_extra_columns' ] );
		add_filter( 'manage_pages_columns', [ $this, 'add_extra_columns' ] );

		add_action( 'quick_edit_custom_box', [ $this, 'edit_fields' ], 10, 2 );
		add_action( 'bulk_edit_custom_box', [ $this, 'edit_fields' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );

		add_action( 'wp_ajax_ss_quick_edit', [ $this, 'ss_save_quick_edit' ] );
		add_action( 'wp_ajax_ss_save_bulk', [ $this, 'ss_save_bulk_edit' ] );
	}

	function add_extra_columns( $column_array ) {
		$column_array['slim_seo[title]'] = 'Meta title';
		$column_array['slim_seo[description]'] = 'Meta description';
		$column_array['slim_seo[noindex]'] = 'Index';
		return $column_array;
	}
	function populate_columns( $column_name, $id ) {
		$data = $this->post->get_data();
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
		wp_enqueue_script( 'slim-seo-populate', SLIM_SEO_URL . 'js/bulk.js', [], SLIM_SEO_VER, true );
	}
	public function ss_save_bulk_edit() {
	    if ( ! wp_verify_nonce( $_POST['nonce'], 'save' ) || empty( $_POST[ 'post_ids' ] ) ) {
			die();
		}

		$data = isset( $_POST['slim_seo'] ) ? wp_unslash( $_POST['slim_seo'][0] ) : [];
		$data = $this->sanitize( $data );

		if ( empty( $data ) ) {
			return;
		}
		foreach( $_POST[ 'post_ids' ] as $post_id ) {
			update_metadata( $this->object_type, $post_id, 'slim_seo', $data );
		}
	}
	public function ss_save_quick_edit() {
		if ( empty( $_POST[ 'post_id' ] ) ) {
			wp_send_json_error( __( 'No post selected', 'slim-seo' ), 400 );
		}
		$data = get_metadata( $this->object_type, 60, 'slim_seo', true );

		wp_send_json_success( [
			'message'  => 'success',
			'slim_seo' => $data,
		] );
		die;
	}
	private function sanitize( $data ) {
		$data = array_merge( $this->defaults, $data );

		$data['title']       = sanitize_text_field( $data['title'] );
		$data['description'] = sanitize_text_field( $data['description'] );
		$data['noindex']     = $data['noindex'] ? 1 : 0;

		return array_filter( $data );
	}
}