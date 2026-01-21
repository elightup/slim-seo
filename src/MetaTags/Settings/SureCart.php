<?php
namespace SlimSEO\MetaTags\Settings;

class SureCart extends Base {
	public function is_active(): bool {
		return is_plugin_active('surecart/surecart.php');
	}

	public function setup(): void {
		$this->object_type = 'post';

		add_filter( 'sc_display_product_seo_meta', '__return_false' );

		add_action( 'admin_print_styles-surecart_page_sc-products', [ $this, 'enqueue' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		// add_action( 'add_meta_box_sc_product', [ $this, 'add_meta_box' ] );
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );

		add_action( 'save_post', [ $this, 'save' ] );
		add_filter( 'slim_seo_variables', [ $this, 'add_variables' ] );
		add_filter( 'slim_seo_data', [ $this, 'add_data' ], 10, 3 );
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

	public function remove_post_types( array $post_types ): array {
		$unsupported = [
			'sc_upsell',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}

	public function add_variables( $variables ) {
		$variables[] = [
			'label'   => 'SureCart',
			'options' => [
				'productsc.sku'         => __( 'SKU', 'slim-seo' ),
				'productsc.name'        => __( 'Name', 'slim-seo' ),
				'productsc.price'       => __( 'Price', 'slim-seo' ),
				'productsc.stock'       => __( 'Stock status', 'slim-seo' ),
				'productsc.currency'    => __( 'Currency', 'slim-seo' ),
				'productsc.offer_count' => __( 'Offer count (variable product)', 'slim-seo' ),
			],
		];

		return $variables;
	}

	public function add_data( array $data, int $post_id, int $term_id ): array {
		// Check if the current post is a single SureCart product.
		if ( ! is_singular('sc_product') ) {
			return $data;
		}

		// Get the active SureCart product if on the product detail page.
		$product = sc_get_product();
		if ( empty( $product ) ) {
		return $data;
		}

		$sku      = $product->sku;
		$name     = $product->name;
		$currency = $product->metrics->currency;
		$price    = $product->display_amount;
		$stock    = __( 'In stock', 'slim-seo' );
		if ( $product->is_out_of_stock ) {
			$stock = __( 'Out of stock', 'slim-seo' );
		}

		$offer_count = 0;
		if ( $product->variants ) {
			$offer_count = count( $product->variants->data );
		}

		$data['productsc'] = compact(
			'sku',
			'name',
			'price',
			'stock',
			'offer_count',
			'currency',
		);

		return $data;
	}
}
