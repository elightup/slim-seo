<?php
namespace SlimSEO;

use eLightUp\SlimSEO\Common\Settings\Page as SettingsPage;
use eLightUp\SlimSEO\Common\Settings\Post as SettingsPost;

class Container {
	private $services = [];

	public function register_services() {
		// Shortcut.
		$services = &$this->services;

		$services['core'] = new Core;

		$services['upgrade'] = new Upgrade;

		$services['featured_plugins'] = new FeaturedPlugins;

		$services['meta_tags_hook']   = new MetaTags\Hook;
		$services['canonical_url']    = new MetaTags\CanonicalUrl;
		$services['meta_title']       = new MetaTags\Title;
		$services['meta_description'] = new MetaTags\Description;
		$services['meta_robots']      = new MetaTags\Robots( $services['canonical_url'] );

		$services['settings_post']    = new MetaTags\Settings\Post;
		$services['settings_term']    = new MetaTags\Settings\Term;
		$services['settings_preview'] = new MetaTags\Settings\Preview;

		$services['sitemaps']   = new Sitemaps\Manager;
		$services['images_alt'] = new ImagesAlt;

		$services['oxygen']          = new Integrations\Oxygen;
		$services['elementor']       = new Integrations\Elementor;
		$services['beaver_builder']  = new Integrations\BeaverBuilder;
		$services['breakdance']      = new Integrations\Breakdance;
		$services['bricks']          = new Integrations\Bricks;
		$services['zion']            = new Integrations\ZionBuilder;
		$services['divi']            = new Integrations\Divi;
		$services['mylisting']       = new Integrations\MyListing;
		$services['forminator']      = new Integrations\Forminator;
		$services['meta_box']        = new Integrations\MetaBox\MetaBox;
		$services['woocommerce']     = new Integrations\WooCommerce;
		$services['acf']             = new Integrations\ACF\ACF;
		$services['kadence']         = new Integrations\Kadence;
		$services['visual_composer'] = new Integrations\VisualComposer;
		$services['polylang']        = new Integrations\Polylang;
		$services['wpml']            = new Integrations\WPML;

		$services['settings']           = new Settings\Settings;
		$services['code']               = new Code( $services['settings'] );
		$services['meta_tags_rest_api'] = new Settings\MetaTags\RestApi;

		$services['redirection'] = new Redirection\Loader;
		$services['robots_txt']  = new RobotsTxt\Loader( $services['settings'] );
		$services['breadcrumbs'] = new Breadcrumbs;

		$services['rest_api'] = new RestApi( $services['meta_title'], $services['meta_description'] );

		$services['no_category_base'] = new NoCategoryBase;

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
		$services['open_graph']    = new MetaTags\OpenGraph(
			$services['meta_title'],
			$services['meta_description'],
			$services['canonical_url']
		);
		$services['twitter_cards'] = new MetaTags\TwitterCards;
		$services['linkedin']      = new MetaTags\LinkedIn;
		$services['feed']          = new Feed;

		$services['schema'] = new Schema\Manager(
			$services['meta_title'],
			$services['meta_description'],
			$services['breadcrumbs'],
			$services['canonical_url']
		);

		$services['auto_listings']   = new Integrations\AutoListings;
		$services['genesis']         = new Integrations\Genesis;
		$services['lifterlms']       = new Integrations\LifterLMS;
		$services['jetpack']         = new Integrations\Jetpack;
		$services['translatepress']  = new Integrations\TranslatePress;
		$services['amp']             = new Integrations\AMP( $services['schema'] );
		$services['affiliatewp']     = new Integrations\AffiliateWP;
		$services['senseilms']       = new Integrations\SenseiLMS;
		$services['wpforo']          = new Integrations\WPForo( $services['meta_tags_hook'] );
		$services['web_stories']     = new Integrations\WebStories(
			$services['open_graph'],
			$services['twitter_cards'],
			$services['linkedin'],
			$services['schema']
		);
		$services['ultimate_member'] = new Integrations\UltimateMember(
			$services['meta_tags_hook'],
			$services['meta_robots']
		);

		$services['the_events_calendar'] = new Integrations\TheEventsCalendar;
		$services['generateblocks'] = new Integrations\GenerateBlocks;
	}

	public function init() {
		do_action( 'slim_seo_init', $this );

		SettingsPage::setup();
		SettingsPost::setup();

		$settings = $this->services['settings'];

		foreach ( $this->services as $id => $service ) {
			if ( ! $settings->is_feature_active( $id ) ) {
				if ( method_exists( $service, 'deactivate' ) ) {
					$service->deactivate();
				}

				continue;
			}

			if ( method_exists( $service, 'is_active' ) && ! $service->is_active() ) {
				continue;
			}

			$service->setup();
		}
	}

	/**
	 * Developers: use this function to disable the services you don't want.
	 */
	public function disable( $id ) {
		unset( $this->services[ $id ] );
	}
}
