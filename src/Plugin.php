<?php
namespace SlimSEO;

class Plugin {
	public function __construct() {
		$title = new MetaTags\Title;
		$description = new MetaTags\Description;

		new MetaTags\OpenGraph( $title, $description );
		new MetaTags\TwitterCards;
		new MetaTags\Settings\Post;
		new MetaTags\Settings\Term;

		new Sitemaps\Manager;
		new ImagesAlt;
		$breadcrumbs = new Breadcrumbs;

		if ( is_admin() ) {
			new Settings;
			new Notification;
			new Migration;
			return;
		}

		new AutoRedirection;
		new Feed;
		new MetaTags\Robots;
		new Cleaner;

		new Schema\Provider( $title, $description, $breadcrumbs );
		new Code;

		new Integrations\WooCommerce;
		new Integrations\Genesis;
		new Integrations\BeaverBuilder;
	}
}
