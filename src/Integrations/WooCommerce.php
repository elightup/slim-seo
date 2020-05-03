<?php
namespace SlimSEO\Integrations;

class WooCommerce {
	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		add_filter( 'slim_seo_meta_description', [ $this, 'no_description' ] );

		add_filter( 'slim_seo_meta_title', [ $this, 'shop_title' ], 10, 2 );
		add_filter( 'slim_seo_meta_description', [ $this, 'shop_description' ], 10, 2 );
		add_filter( 'slim_seo_robots_index', [ $this, 'shop_index' ] );
	}

	public function no_description( $description ) {
		/*
		 * Strip all shortcodes for WooCommerce pages since they do some logic like setting errors in the session.
		 * Processing these shortcodes might break WooCommerce (like clearing notices in the session).
		 *
		 * @see https://wordpress.org/support/topic/woocommerce-notices-are-not-showing-after-activating-your-plugin/
		 */
		return $this->is_disabled_page() ? strip_shortcodes( $description ) : $description;
	}

	private function is_disabled_page() {
		$pages = [ 'cart', 'checkout', 'myaccount' ];
		$pages = array_map( 'wc_get_page_id', $pages );
		return is_page( $pages );
	}

	public function shop_title( $title, $title_obj ) {
		return is_shop() ? $title_obj->get_singular_value( wc_get_page_id( 'shop' ) ) : $title;
	}

	public function shop_description( $description, $description_obj ) {
		return is_shop() ? $description_obj->get_singular_value( wc_get_page_id( 'shop' ) ) : $description;
	}

	public function shop_index( $is_indexed ) {
		if ( ! is_shop() ) {
			return $is_indexed;
		}
		$data = get_post_meta( wc_get_page_id( 'shop' ), 'slim_seo', true );
		if ( ! empty( $data['noindex'] ) ) {
			return false;
		}
		return $is_indexed;
	}
}