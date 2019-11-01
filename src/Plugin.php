<?php
namespace SlimSEO;

class Plugin {
	public function __construct() {
		$title = new MetaTags\Title;
		$description = new MetaTags\Description;

		new MetaTags\OpenGraph( $title, $description );
		new MetaTags\Twitter;
		new MetaTags\Settings\Post;
		new MetaTags\Settings\Term;

		new Sitemaps\Manager;
		new ImagesAlt;
		$breadcrumbs = new Breadcrumbs;

		if ( is_admin() ) {
			new Settings;
			new Notification;
			return;
		}

		new AutoRedirection;
		new Feed;
		new MetaTags\Robots;
		new Cleaner;
		new WooCommerce;

		new Schema\Provider( $title, $description, $breadcrumbs );
		new Schema\Disabler;
		new Code;
	}
}
