<?php
namespace SlimSEO;

class Plugin {
	private $services = [];

	public function register_services() {
		// Shortcut.
		$services = &$this->services;

		$services['canonical_url']    = new MetaTags\CanonicalUrl;
		$services['meta_title']       = new MetaTags\Title;
		$services['meta_description'] = new MetaTags\Description;
		$services['meta_robots']      = new MetaTags\Robots( $services['canonical_url'] );

		$services['settings_post'] = new MetaTags\Settings\Post;
		$services['settings_term'] = new MetaTags\Settings\Term;

		$services['sitemaps']   = new Sitemaps\Manager;
		$services['images_alt'] = new ImagesAlt;

		$services['oxygen']         = new Integrations\Oxygen;
		$services['elementor']      = new Integrations\Elementor;
		$services['beaver_builder'] = new Integrations\BeaverBuilder;
		$services['bricks']         = new Integrations\Bricks;
		$services['zion']           = new Integrations\ZionBuilder;

		$services['settings'] = new Settings\Settings;
		$services['code']     = new Code( $services['settings'] );

		$services['redirection'] = new Redirection\Loader;
		$services['breadcrumbs'] = new Breadcrumbs;

		// Admin only.
		if ( is_admin() ) {
			$services['link_attributes']    = new LinkAttributes;
			$services['notification']       = new Notification;
			$services['migration']          = new Migration\Migration;
			$services['admin_columns_post'] = new MetaTags\AdminColumns\Post(
				$services['settings_post'],
				$services['meta_title'],
				$services['meta_description'],
				$services['meta_robots']
			);
			$services['admin_columns_term'] = new MetaTags\AdminColumns\Term(
				$services['settings_term'],
				$services['meta_title'],
				$services['meta_description'],
				$services['meta_robots']
			);
			return;
		}

		// Front-end only.
		$services['rel_links']     = new MetaTags\RelLinks;
		$services['open_graph']    = new MetaTags\OpenGraph( $services['meta_title'], $services['meta_description'], $services['canonical_url'] );
		$services['twitter_cards'] = new MetaTags\TwitterCards;
		$services['feed']          = new Feed;

		$services['schema'] = new Schema\Manager( $services['meta_title'], $services['meta_description'], $services['breadcrumbs'] );

		$services['woocommerce']     = new Integrations\WooCommerce;
		$services['auto_listings']   = new Integrations\AutoListings;
		$services['genesis']         = new Integrations\Genesis;
		$services['lifterlms']       = new Integrations\LifterLMS;
		$services['jetpack']         = new Integrations\Jetpack;
		$services['polylang']        = new Integrations\Polylang;
		$services['wpml']            = new Integrations\WPML;
		$services['amp']             = new Integrations\AMP( $services['schema'] );
		$services['divi']            = new Integrations\Divi;
		$services['metabox']         = new Integrations\MetaBox;
		$services['affiliatewp']     = new Integrations\AffiliateWP;
		$services['web_stories']     = new Integrations\WebStories(
			$services['open_graph'],
			$services['twitter_cards'],
			$services['schema']
		);
		$services['ultimate_member'] = new Integrations\UltimateMember(
			$services['meta_description'],
			$services['open_graph'],
			$services['twitter_cards'],
			$services['meta_robots']
		);
	}

	public function init() {
		do_action( 'slim_seo_init', $this );

		Settings\Page::setup();
		$settings = $this->services['settings'];
		foreach ( $this->services as $id => $service ) {
			if ( $settings->is_feature_active( $id ) ) {
				$service->setup();
			}
		}

		if ( is_admin() ) {
			new Dashboard( 'https://feeds.feedburner.com/slimseo', 'https://wpslimseo.com/', [
				'title'           => 'Slim SEO',
				'dismiss_tooltip' => esc_html__( 'Dismiss all Slim SEO news', 'slim-seo' ),
				'dismiss_confirm' => esc_html__( 'Are you sure to dismiss all Slim SEO news?', 'slim-seo' ),
			] );
		}
	}

	/**
	 * Developers: use this function to disable the services you don't want.
	 */
	public function disable( $id ) {
		unset( $this->services[ $id ] );
	}
}
