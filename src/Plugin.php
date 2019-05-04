<?php
namespace SlimSEO;

class Plugin {
	private $services = [];

	public function register_services() {
		$this->title = new MetaTags\Title;
		$this->description = new MetaTags\Description;
		$this->opengraph = new MetaTags\OpenGraph( $this->title, $this->description );
		$this->twitter = new MetaTags\Twitter;

		$this->sitemap_manager = new Sitemaps\Manager;
		$this->image_alt = new ImagesAlt;
		$this->breadcrumbs = new Breadcrumbs;

		if ( is_admin() ) {
			$this->notification = new Notification;
			return;
		}

		$this->auto_redirection = new AutoRedirection;
		$this->feed = new Feed;
		$this->robots = new MetaTags\Robots;
		$this->cleaner = new Cleaner;

		add_action( 'wp_footer', [ $this, 'register_schema_services' ] );
	}

	public function register_schema_services() {
		$manager = new Schema\Manager;

		$website = new Schema\Types\Website( home_url( '/' ) );
		$manager->add_entity( $website );

		$search_action = new Schema\Types\SearchAction();
		$website->add_child( 'potentialAction', $search_action );
		$manager->add_entity( $search_action );

		$breadcrumbs = new Schema\Types\Breadcrumbs;
		$breadcrumbs->source = $this->breadcrumbs;
		$manager->add_entity( $breadcrumbs );

		$webpage = new Schema\Types\WebPage;
		$webpage->title = $this->title;
		$webpage->description = $this->description;
		$webpage->set_parent( $website );
		$webpage->add_child( 'breadcrumb', $breadcrumbs );
		$manager->add_entity( $webpage );

		if ( is_singular() && has_post_thumbnail() ) {
			$thumbnail = new Schema\Types\ImageObject( null, 'thumbnail' );
			$thumbnail->image_id = get_post_thumbnail_id();

			$webpage->add_child( 'primaryImageOfPage', $thumbnail );
			$webpage->add_child( 'image', $thumbnail );
			$manager->add_entity( $thumbnail );
		}

		$manager->output();
	}

	public function __get( $name ) {
		return isset( $this->services[ $name ] ) ? $this->services[ $name ] : null;
	}

	public function __set( $name, $service ) {
		$this->services[ $name ] = $service;
	}
}
