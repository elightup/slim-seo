<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Title;

class WooCommerce {
	private $shop_page_id;

	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description', [ $this, 'no_description' ] );

		if ( ! is_shop() ) {
			return;
		}

		$this->shop_page_id = (int) wc_get_page_id( 'shop' );

		add_filter( 'post_type_archive_title', [ $this, 'set_page_title_as_archive_title' ] );
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
		return $this->no_shortcodes_page() ? strip_shortcodes( $description ) : $description;
	}

	private function no_shortcodes_page() {
		$pages = [ 'cart', 'checkout', 'myaccount' ];
		$pages = array_map( 'wc_get_page_id', $pages );
		return is_page( $pages );
	}

	public function set_page_title_as_archive_title() : string {
		return get_the_title( $this->shop_page_id );
	}

	public function shop_title( $title, Title $title_obj ) {
		return $title_obj->get_singular_value( $this->shop_page_id ) ?: $title;
	}

	public function shop_description( $description, Description $description_obj ) {
		return $description_obj->get_singular_value( $this->shop_page_id ) ?: $description;
	}

	public function shop_index( $is_indexed ) {
		$data = get_post_meta( $this->shop_page_id, 'slim_seo', true );
		return empty( $data['noindex'] ) ? $is_indexed : false;
	}
}
