<?php
namespace SlimSEO\Integrations;

class WooCommerce {
	private $tags = [
		'product:price:amount',
		'product:price:currency',
		'og:price:standard_amount',
		'og:availability',
		'og:type',
	];
	private $product;

	public function is_active(): bool {
		return class_exists( 'WooCommerce' );
	}

	public function setup(): void {
		add_action( 'template_redirect', [ $this, 'process' ] );
		add_filter( 'slim_seo_variables', [ $this, 'add_variables' ] );
		add_filter( 'slim_seo_data', [ $this, 'add_data' ], 10, 3 );
	}

	public function process(): void {
		add_filter( 'slim_seo_breadcrumbs_args', [ $this, 'change_breadcrumbs_taxonomy' ] );

		if ( $this->is_skipped_page() ) {
			add_filter( 'slim_seo_post_content', '__return_empty_string' );
		}

		add_filter( 'slim_seo_allowed_shortcodes', [ $this, 'exclude_shortcodes' ] );

		if ( is_singular( 'product' ) ) {
			$this->add_pinterest_pins();
		}
	}

	public function change_breadcrumbs_taxonomy( array $args ): array {
		if ( is_singular( 'product' ) ) {
			$args['taxonomy'] = 'product_cat';
		}
		return $args;
	}

	private function is_skipped_page(): bool {
		return is_cart() || is_checkout() || is_account_page();
	}

	public function exclude_shortcodes( array $shortcodes ): array {
		return array_filter( $shortcodes, function ( $callback ): bool {
			return ! is_string( $callback ) || ! str_starts_with( $callback, 'WC_Shortcodes' );
		} );
	}

	private function add_pinterest_pins() {
		$this->product = wc_get_product( get_queried_object() );
		if ( empty( $this->product ) ) {
			return;
		}

		add_filter( 'slim_seo_open_graph_tags', [ $this, 'og_tags' ] );

		foreach ( $this->tags as $tag ) {
			$short_name = strtr( $tag, [
				'og:' => '',
				':'   => '_',
			] );
			add_filter( "slim_seo_open_graph_{$short_name}", [ $this, "og_$short_name" ] );
		}
	}

	public function og_tags( array $tags ): array {
		return array_merge( $tags, $this->tags );
	}

	public function og_type(): string {
		return 'product';
	}

	public function og_product_price_amount() {
		return wc_get_price_to_display( $this->product );
	}

	public function og_product_price_currency(): string {
		return get_woocommerce_currency();
	}

	public function og_price_standard_amount() {
		return $this->product->is_on_sale() ? $this->product->get_regular_price() : '';
	}

	public function og_availability(): string {
		$statuses = [
			'instock'     => 'instock',
			'outofstock'  => 'out of stock',
			'onbackorder' => 'backorder',
		];

		$status = $this->product->get_stock_status();

		return $statuses[ $status ] ?? '';
	}

	public function add_variables( $variables ) {
		$variables[] = [
			'label'   => 'WooCommerce',
			'options' => [
				'product.price'          => __( 'Price', 'slim-seo-schema' ),
				'product.price_with_tax' => __( 'Price including tax', 'slim-seo-schema' ),
				'product.sale_from'      => __( 'Sale price date "From"', 'slim-seo-schema' ),
				'product.sale_to'        => __( 'Sale price date "To"', 'slim-seo-schema' ),
				'product.sku'            => __( 'SKU', 'slim-seo-schema' ),
				'product.stock'          => __( 'Stock status', 'slim-seo-schema' ),
				'product.currency'       => __( 'Currency', 'slim-seo-schema' ),
				'product.rating'         => __( 'Rating value', 'slim-seo-schema' ),
				'product.review_count'   => __( 'Review count', 'slim-seo-schema' ),
				'product.low_price'      => __( 'Low price (variable product)', 'slim-seo-schema' ),
				'product.high_price'     => __( 'High price (variable product)', 'slim-seo-schema' ),
				'product.offer_count'    => __( 'Offer count (variable product)', 'slim-seo-schema' ),
			],
		];

		return $variables;
	}

	private function get_product( $post_id ) {
		$post = $post_id ?: ( is_singular() ? get_queried_object() : get_post() );
		return wc_get_product( $post );
	}

	public function add_data( array $data, int $post_id, int $term_id ): array {
		$product = $this->get_product( $post_id );

		if ( empty( $product ) ) {
			return $data;
		}

		$price          = $product->get_price();
		$price_with_tax = wc_get_price_including_tax( $product, [ 'price' => $price ] );

		$sale_from = '';
		if ( $product->get_date_on_sale_from() ) {
			$sale_from = gmdate( 'Y-m-d', $product->get_date_on_sale_from()->getTimestamp() );
		}

		// By default, set the sale price is today + 1 month.
		$today   = gmdate( 'Y-m-d' );
		$sale_to = gmdate( 'Y-m-d', wc_string_to_timestamp( '+1 month' ) );

		// Sale already started.
		if ( $product->is_on_sale() ) {
			if ( $product->get_date_on_sale_to() ) {
				$sale_to = gmdate( 'Y-m-d', $product->get_date_on_sale_to()->getTimestamp() );
			}
		} else {
			// Sale hasn't started yet, so the regular price will be available until sale!
			if ( $sale_from > $today ) {
				$sale_to = gmdate( 'Y-m-d', wc_string_to_timestamp( $sale_from ) - DAY_IN_SECONDS );
			} elseif ( $sale_from === $today ) {
				$sale_to = $today;
			}
		}

		$low_price   = '';
		$high_price  = '';
		$offer_count = 0;
		if ( $product->is_type( 'variable' ) ) {
			$low_price   = $product->get_variation_price( 'min', false );
			$low_price   = wc_get_price_including_tax( $product, [ 'price' => $low_price ] );
			$high_price  = $product->get_variation_price( 'max', false );
			$high_price  = wc_get_price_including_tax( $product, [ 'price' => $high_price ] );
			$offer_count = count( $product->get_children() );
		}

		$sku          = $product->get_sku();
		$currency     = get_woocommerce_currency();
		$rating       = $product->get_average_rating();
		$review_count = $product->get_review_count();

		$status = strtolower( $product->get_stock_status() );
		$stock  = $this->get_stock_statuses()[ $status ] ?? __( 'In stock', 'slim-seo' );

		$data['product'] = compact(
			'price',
			'price_with_tax',
			'low_price',
			'high_price',
			'offer_count',
			'sale_from',
			'sale_to',
			'sku',
			'stock',
			'currency',
			'rating',
			'review_count',
		);

		return $data;
	}

	private function get_stock_statuses(): array {
		return [
			// WooCommerce built-in statuses.
			'instock'              => __( 'In stock', 'slim-seo' ),
			'outofstock'           => __( 'Out of stock', 'slim-seo' ),
			'onbackorder'          => __( 'Back order', 'slim-seo' ),

			// Developers can register product custom stock statuses (supported by Google) with variations.
			'discontinued'         => __( 'Discontinued', 'slim-seo' ),

			'instoreonly'          => __( 'In store only', 'slim-seo' ),
			'in_store_only'        => __( 'In store only', 'slim-seo' ),
			'in-store-only'        => __( 'In store only', 'slim-seo' ),

			'limitedavailability'  => __( 'Limited availability', 'slim-seo' ),
			'limited_availability' => __( 'Limited availability', 'slim-seo' ),
			'limited-availability' => __( 'Limited availability', 'slim-seo' ),

			'onlineonly'           => __( 'Online only', 'slim-seo' ),
			'online_only'          => __( 'Online only', 'slim-seo' ),
			'online-only'          => __( 'Online only', 'slim-seo' ),

			'preorder'             => __( 'Pre order', 'slim-seo' ),
			'pre_order'            => __( 'Pre order', 'slim-seo' ),
			'pre-order'            => __( 'Pre order', 'slim-seo' ),

			'presale'              => __( 'Pre sale', 'slim-seo' ),
			'pre_sale'             => __( 'Pre sale', 'slim-seo' ),
			'pre-sale'             => __( 'Pre sale', 'slim-seo' ),

			'soldout'              => __( 'Sold out', 'slim-seo' ),
			'sold_out'             => __( 'Sold out', 'slim-seo' ),
			'sold-out'             => __( 'Sold out', 'slim-seo' ),
		];
	}
}
