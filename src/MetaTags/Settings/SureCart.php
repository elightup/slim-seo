<?php
namespace SlimSEO\MetaTags\Settings;

use SlimSEO\Helpers\Data;

class SureCart extends Base {
	public function is_active(): bool {
		return is_plugin_active('surecart/surecart.php');
	}

	public function setup(): void {
		$this->object_type = 'post';

		add_filter( 'sc_display_product_seo_meta', '__return_false' );
		add_filter( 'sc_display_product_json_ld_schema', '__return_false' );

		add_action( 'admin_print_styles-surecart_page_sc-products', [ $this, 'enqueue' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function enqueue(): void {
		parent::enqueue();
	}

	public function add_meta_box() {
		$post_types = $this->get_types();
		foreach ( $post_types as $post_type ) {
			add_meta_box( 'slim-seo', __( 'Search Engine Optimization', 'slim-seo' ), [ $this, 'render' ], $post_type, 'normal', 'low' );
		}
	}

	public function get_types() {
		return [ 'sc_product' ];
	}

	protected function get_object_id() {
		return get_the_ID();
	}
}
