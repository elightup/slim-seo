<?php
namespace SlimSEO\Integrations;

class Helper {
	public static function is_woo_active(): bool {
		$woo = new WooCommerce;
		return $woo->is_active();
	}
}