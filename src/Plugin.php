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

		$this->schema_provider = new Schema\Provider( $this->title, $this->description, $this->breadcrumbs );
		$this->schema_disabler = new Schema\Disabler;
	}

	public function __get( $name ) {
		return isset( $this->services[ $name ] ) ? $this->services[ $name ] : null;
	}

	public function __set( $name, $service ) {
		$this->services[ $name ] = $service;
	}
}
