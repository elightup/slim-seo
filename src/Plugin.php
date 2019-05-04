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
		$website->add_reference( 'potentialAction', $search_action );
		$manager->add_entity( $search_action );

		$breadcrumbs = new Schema\Types\Breadcrumbs;
		$breadcrumbs->source = $this->breadcrumbs;
		$manager->add_entity( $breadcrumbs );

		$webpage = new Schema\Types\WebPage;
		$webpage->title = $this->title;
		$webpage->description = $this->description;
		$webpage->add_reference( 'isPartOf', $website );
		$webpage->add_reference( 'breadcrumb', $breadcrumbs );
		$manager->add_entity( $webpage );

		if ( is_singular() && has_post_thumbnail() ) {
			$thumbnail = new Schema\Types\ImageObject( null, 'thumbnail' );
			$thumbnail->image_id = get_post_thumbnail_id();

			$webpage->add_reference( 'primaryImageOfPage', $thumbnail );
			$webpage->add_reference( 'image', $thumbnail );
			$manager->add_entity( $thumbnail );
		}

		if ( is_single( 'post' ) ) {
			$article = new Schema\Types\Article();
			$article->post = get_queried_object();
			$article->add_reference( 'isPartOf', $webpage );
			$article->add_property( 'mainEntityOfPage', $webpage->id );
			$manager->add_entity( $article );

			$author = new Schema\Types\Person( null, 'author' );
			$author->user = get_userdata( $article->post->post_author );

			$author_image = new Schema\Types\ImageObject();
			$author_image->add_property( 'url', get_avatar_url( $author->user->ID ) );
			$author_image->add_property( 'width', 96 );
			$author_image->add_property( 'height', 96 );
			$author_image->add_property( 'caption', $author->user->display_name );

			$author->add_reference( 'image', $author_image );

			$manager->add_entity( $author );
			$manager->add_entity( $author_image );

			$article->add_reference( 'author', $author );

			if ( has_post_thumbnail() ) {
				$article->add_reference( 'image', $thumbnail );
			}
		}

		if ( is_author() ) {
			$author = new Schema\Types\Person( null, 'author' );
			$author->user = get_queried_object();
			$author->add_reference( 'mainEntityOfPage', $webpage );

			$author_image = new Schema\Types\ImageObject();
			$author_image->add_property( 'url', get_avatar_url( $author->user->ID ) );
			$author_image->add_property( 'width', 96 );
			$author_image->add_property( 'height', 96 );
			$author_image->add_property( 'caption', $author->user->display_name );

			$author->add_reference( 'image', $author_image );

			$manager->add_entity( $author );
			$manager->add_entity( $author_image );
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
