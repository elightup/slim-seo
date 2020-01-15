<?php
namespace SlimSEO;

class Plugin {
	private $services = [];

	public function register_services() {
		$this->services['meta_title'] = new MetaTags\Title;
		$this->services['meta_description'] = new MetaTags\Description;

		$this->services['open_graph'] = new MetaTags\OpenGraph( $this->services['meta_title'], $this->services['meta_description'] );
		$this->services['twitter_cards'] = new MetaTags\TwitterCards;

		$this->services['settings_post'] = new MetaTags\Settings\Post;
		$this->services['settings_term'] = new MetaTags\Settings\Term;

		$this->services['sitemaps'] = new Sitemaps\Manager;
		$this->services['images_alt'] = new ImagesAlt;
		$this->services['breadcrumbs'] = new Breadcrumbs;

		// Admin only.
		if ( is_admin() ) {
			$this->services['settings'] = new Settings;
			$this->services['notification'] = new Notification;
			$this->services['migration'] = new Migration;
			return;
		}

		// Front-end only.
		$this->services['auto_redirection'] = new AutoRedirection;
		$this->services['feed'] = new Feed;
		$this->services['meta_robots'] = new MetaTags\Robots;
		$this->services['cleaner'] = new Cleaner;

		$this->services['shema'] = new Schema\Provider( $this->services['meta_title'], $this->services['meta_description'], $this->services['breadcrumbs'] );
		$this->services['code'] = new Code;

		$this->services['woocommerce'] = new Integrations\WooCommerce;
		$this->services['genesis'] = new Integrations\Genesis;
		$this->services['beaver_builder'] = new Integrations\BeaverBuilder;
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
