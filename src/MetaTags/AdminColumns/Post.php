<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Helper;

class Post extends Base {
	private $types;

	public function setup() {
		// Run init in 99 to gel all custom post types
		add_action( 'init', [ $this, 'setup_admin' ], 99 );
	}

	public function setup_admin() {
		$this->types = $this->settings->get_types();
		foreach ( $this->types as $type ) {
			add_filter( "manage_{$type}_posts_columns", [ $this, 'columns' ] );
			add_action( "manage_{$type}_posts_custom_column", [ $this, 'render' ], 10, 2 );
		}

		add_action( 'admin_print_styles-edit.php', [ $this, 'enqueue' ] );

		// Quick edit.
		add_action( 'quick_edit_custom_box', [ $this, 'output_quick_edit_fields' ], 10, 2 );
		add_action( 'wp_ajax_ss_quick_edit', [ $this, 'get_quick_edit_data' ] );

		// Bulk edit.
		add_action( 'bulk_edit_custom_box', [ $this, 'output_bulk_edit_fields' ], 10, 2 );
		add_action( 'wp_ajax_ss_save_bulk', [ $this, 'save_bulk_edit' ] );
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
				echo empty( $data['noindex'] ) ? '<span class="ss-success"></span>' : '<span class="ss-danger"></span>';
				break;
		}
	}

	public function enqueue() {
		if ( ! in_array( get_current_screen()->post_type, $this->types, true ) ) {
			return;
		}
		wp_enqueue_style( 'slim-seo-edit', SLIM_SEO_URL . 'css/edit.css', [], SLIM_SEO_VER );
		wp_enqueue_script( 'slim-seo-bulk', SLIM_SEO_URL . 'js/bulk.js', [], SLIM_SEO_VER, true );
	}

	public function output_quick_edit_fields( $column_name, $post_type ) {
		if ( 'meta_title' !== $column_name ) {
			return;
		}
		wp_nonce_field( 'save', 'ss_nonce' );
		?>
		<p class="wp-clearfix"></p>
		<fieldset class="inline-edit-col-left">
			<legend class="inline-edit-legend"><?php esc_html_e( 'Search Engine Optimization', 'slim-seo' ) ?></legend>
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php esc_html_e( 'Meta title', 'slim-seo' ) ?></span>
					<span class="input-text-wrap">
						<input type="text" name="slim_seo[title]" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php esc_html_e( 'Meta desc.', 'slim-seo' ) ?></span>
					<span class="input-text-wrap">
						<textarea name="slim_seo[description]" value=""></textarea>
					</span>
				</label>
				<div class="inline-edit-group wp-clearfix">
					<label class="alignleft">
						<input type="checkbox" name="slim_seo[noindex]" value="1">
						<span class="checkbox-title"><?php esc_html_e( 'Hide from search results', 'slim-seo' ) ?></span>
					</label>
				</div>
			</div>
		</fieldset>
		<?php
	}

	public function get_quick_edit_data() {
		check_ajax_referer( 'save', 'nonce' );
		if ( empty( $_GET['post_id'] ) ) {
			wp_send_json_error();
		}
		$data = get_post_meta( $_GET['post_id'], 'slim_seo', true );
		$data = $data ? $data : [];
		$data = array_merge( [
			'title'       => '',
			'description' => '',
			'noindex'     => 0,
		], $data );

		wp_send_json_success( $data );
	}

	public function output_bulk_edit_fields( $column_name, $post_type ) {
		if ( 'meta_title' !== $column_name ) {
			return;
		}
		wp_nonce_field( 'save', 'ss_nonce' );
		?>
		<p class="wp-clearfix"></p>
		<div class="inline-edit-col-left">
			<legend class="inline-edit-legend"><?php esc_html_e( 'Search Engine Optimization', 'slim-seo' ) ?></legend>
			<div class="inline-edit-col">
				<div class="inline-edit-group wp-clearfix">
					<label>
						<span class="title"><?php esc_html_e( 'Hide from search results', 'slim-seo' ) ?></span>
						<select name="noindex">
							<option value="-1"><?php esc_html_e( '— No Change —', 'slim-seo' ) ?></option>
							<option value="1"><?php esc_html_e( 'Yes', 'slim-seo' ) ?></option>
							<option value="0"><?php esc_html_e( 'No', 'slim-seo' ) ?></option>
						</select>
					</label>
				</div>
			</div>
		</div>
		<?php
	}

	public function save_bulk_edit() {
		check_ajax_referer( 'save', 'nonce' );

		if ( empty( $_GET['post_ids'] ) || ! isset( $_GET['noindex'] ) ) {
			wp_send_json_error();
		}

		$noindex = (int) $_GET['noindex'];
		if ( ! in_array( $noindex, [ -1, 0, 1 ], true ) ) {
			wp_send_json_error();
		}

		// Not changed.
		if ( $noindex === -1 ) {
			wp_send_json_success();
		}

		$post_ids = wp_strip_all_tags( wp_unslash( $_GET['post_ids'] ) );
		$post_ids = array_filter( array_map( 'intval', explode( ',', $post_ids ) ) );
		foreach ( $post_ids as $post_id ) {
			$data            = get_post_meta( $post_id, 'slim_seo', true );
			$data            = $data ? $data : [];
			$data['noindex'] = $noindex;

			$data = array_filter( $data );
			update_post_meta( $post_id, 'slim_seo', $data );
		}
		wp_send_json_success();
	}
}
