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
		$args['taxonomy'] = 'product_cat';
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
}
