<?php
namespace SlimSEO\Integrations;

class WooCommerce {
	private $shop_page_id;

	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		add_filter( 'slim_seo_breadcrumbs_args', [ $this, 'change_breadcrumbs_taxonomy' ] );
		add_filter( 'slim_seo_meta_description', [ $this, 'strip_shortcodes' ] );

		if ( ! is_shop() ) {
			return;
		}

		$this->shop_page_id = (int) wc_get_page_id( 'shop' );

		add_filter( 'post_type_archive_title', [ $this, 'set_page_title_as_archive_title' ] );
	}

	public function change_breadcrumbs_taxonomy( array $args ): array {
		$args['taxonomy'] = 'product_cat';
		return $args;
	}

	/**
	 * Strip all shortcodes for WooCommerce pages since they do some logic like setting errors in the session.
	 * Processing these shortcodes might break WooCommerce (like clearing notices in the session).
	 *
	 * @see https://wordpress.org/support/topic/woocommerce-notices-are-not-showing-after-activating-your-plugin/
	 */
	public function strip_shortcodes( $description ) {
		return $this->no_shortcodes_page() ? strip_shortcodes( $description ) : $description;
	}

	private function no_shortcodes_page() {
		$pages = [ 'cart', 'checkout', 'myaccount' ];
		$pages = array_map( 'wc_get_page_id', $pages );
		return is_page( $pages );
	}

	public function set_page_title_as_archive_title(): string {
		return get_the_title( $this->shop_page_id );
	}
}
