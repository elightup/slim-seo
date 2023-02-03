<?php
namespace SlimSEO\Integrations;

class AffiliateWP {
	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! class_exists( 'Affiliate_WP' ) ) {
			return;
		}

		// Do not generate meta description from the affiliate area page's content, because it contains forms.
		if ( is_page( affwp_get_affiliate_area_page_id() ) ) {
			add_filter( 'slim_seo_meta_description_generated', '__return_empty_string' );
		}

		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
	}

	/**
	 * Don't parse AffiliateWP shortcodes for meta description.
	 * @link https://affiliatewp.com/doc-categories/shortcodes/
	 */
	public function skip_shortcodes( $shortcodes ) {
		$shortcodes = array_merge( $shortcodes, [
			'affiliate_conversion_script',
			'affiliate_coupons',
			'affiliate_creatives',
			'non_affiliate_content',
			'affiliate_creative',
			'opt_in',
			'affiliate_content',
			'affiliate_referral_url',
			'affiliate_registration',
			'affiliate_login',
			'affiliate_area',
		] );
		return $shortcodes;
	}
}
