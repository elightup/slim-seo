<?php
namespace SlimSEO;

class Plugin {
	private $services = [];

	public function register_services() {
		$this->services['meta_title'] = new MetaTags\Title;
		$this->services['meta_description'] = new MetaTags\Description;

		$this->services['settings_post'] = new MetaTags\Settings\Post;
		$this->services['settings_term'] = new MetaTags\Settings\Term;

		$this->services['sitemaps'] = new Sitemaps\Manager;
		$this->services['images_alt'] = new ImagesAlt;

		// Admin only.
		if ( is_admin() ) {
			$this->services['settings'] = new Settings\Settings;
			$this->services['notification'] = new Notification;
			$this->services['migration'] = new Migration\Migration;
			return;
		}

		// Front-end only.
		$this->services['canonical_url'] = new MetaTags\CanonicalUrl;
		$this->services['rel_links'] = new MetaTags\RelLinks;
		$this->services['open_graph'] = new MetaTags\OpenGraph( $this->services['meta_title'], $this->services['meta_description'], $this->services['canonical_url'] );
		$this->services['twitter_cards'] = new MetaTags\TwitterCards;
		$this->services['meta_robots'] = new MetaTags\Robots( $this->services['canonical_url'] );
		$this->services['breadcrumbs'] = new Breadcrumbs;
		$this->services['auto_redirection'] = new AutoRedirection;
		$this->services['feed'] = new Feed;
		$this->services['cleaner'] = new Cleaner;

		$this->services['schema'] = new Schema\Manager( $this->services['meta_title'], $this->services['meta_description'], $this->services['breadcrumbs'] );
		$this->services['code'] = new Code;

		$this->services['woocommerce'] = new Integrations\WooCommerce;
		$this->services['genesis'] = new Integrations\Genesis;
		$this->services['beaver_builder'] = new Integrations\BeaverBuilder;
		$this->services['oxygen'] = new Integrations\Oxygen;
		$this->services['lifterlms'] = new Integrations\LifterLMS;
	}

	public function init() {
		do_action( 'slim_seo_init', $this );

		foreach ( $this->services as $service ) {
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
