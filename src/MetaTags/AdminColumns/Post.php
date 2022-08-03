<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Helper;

class Post extends Base {
	public function setup() {
		$types = $this->settings->get_types();
		$this->object_type = 'post';
		foreach ( $types as $type ) {
			add_filter( "manage_{$type}_posts_columns", [ $this, 'columns' ] );
			add_action( "manage_{$type}_posts_custom_column", [ $this, 'render' ], 10, 2 );
		}
		add_action( "quick_edit_custom_box", [ $this, 'edit_fields' ], 10, 2 );
		add_action( "bulk_edit_custom_box", [ $this, 'edit_fields' ], 10, 2 );

		add_action( "admin_enqueue_scripts", [ $this, 'enqueue' ] );
		add_action( "wp_ajax_ss_quick_edit", [ $this, 'ss_get_quick_edit_data' ] );
		add_action( "wp_ajax_ss_save_bulk", [ $this, 'ss_save_bulk_edit' ] );
	}

	public function render( $column, $post_id ) {
		$data = get_post_meta( $post_id, 'slim_seo', true );
		switch ( $column ) {
			case 'meta_title':
				$title = $this->title->get_singular_value( $post_id );
				echo esc_html( Helper::normalize( $title ) );
				break;
			case 'meta_description':
				if ( ! empty( $data['description'] ) ) {
					echo esc_html( Helper::normalize( $data['description'] ) );
				}
				break;
			case 'noindex':
				echo ( ! empty( $data['noindex'] ) && $data['noindex'] == true ) ? 'Yes' : 'No';
				break;
		}
	}
	public function edit_fields( $column_name, $post_type  ) {
		switch( $column_name ) :
			case 'meta_title':
				wp_nonce_field( 'save', 'ss_nonce' );

				echo '<fieldset class="inline-edit-col-right">';
				echo '<div class="ss-field">
						<div class="ss-label">Meta title</div>
						<div class="ss-input"><input type="text" name="slim_seo[title]" value=""></div>
					</div>';
				break;
			case 'meta_description':
				echo '<div class="ss-field">
						<div class="ss-label">Meta description</div>
						<div class="ss-input"><input type="text" name="slim_seo[description]" value=""></div>
					</div>';
				break;
			case 'noindex': 
				echo '<div class="ss-field">
						<div class="ss-label">Hide from search results</div>
						<div class="ss-input"><input type="checkbox" name="slim_seo[noindex]" value="1"></div>
					</div>';
				// Closing the fieldset element on last element
				echo '</fieldset>';
				break;
		endswitch;
	}
	public function enqueue( ) {
		wp_enqueue_style( 'slim-seo-settings', SLIM_SEO_URL . 'css/meta-box.css', [], SLIM_SEO_VER );
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
	public function ss_get_quick_edit_data() {
		if ( empty( $_POST[ 'post_id' ] ) ) {
			wp_send_json_error( __( 'No post selected', 'slim-seo' ), 400 );
		}
		$data = get_metadata( $this->object_type, $_POST[ 'post_id' ], 'slim_seo', true );

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
