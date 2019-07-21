<?php
namespace SlimSEO\Schema;

class Provider {
	private $title;
	private $description;
	private $breadcrumbs;

	public function __construct( $title, $description, $breadcrumbs ) {
		$this->title       = $title;
		$this->description = $description;
		$this->breadcrumbs = $breadcrumbs;

		add_action( 'wp_footer', [ $this, 'output' ] );
		add_action( 'amp_post_template_footer', [ $this, 'output' ] );

		add_action( 'amp_post_template_head', array( $this, 'remove_default_amp_schema' ), 9 );
	}

	public function remove_default_amp_schema() {
		remove_action( 'amp_post_template_head', 'amp_print_schemaorg_metadata' );
	}

	public function output() {
		$manager = new Manager;

		$website = new Types\Website( home_url( '/' ) );
		$manager->add_entity( $website );

		$search_action = new Types\SearchAction();
		$website->add_reference( 'potentialAction', $search_action );
		$manager->add_entity( $search_action );

		$breadcrumbs = new Types\Breadcrumbs;
		$breadcrumbs->source = $this->breadcrumbs;
		$manager->add_entity( $breadcrumbs );

		$webpage = new Types\WebPage;
		$webpage->title = $this->title;
		$webpage->description = $this->description;
		$webpage->add_reference( 'isPartOf', $website );
		$webpage->add_reference( 'breadcrumb', $breadcrumbs );
		$manager->add_entity( $webpage );

		$organization = new Types\Organization( home_url( '/' ) );
		$website->add_reference( 'publisher', $organization );
		$manager->add_entity( $organization );

		$logo_id = get_option( 'site_icon' );
		if ( current_theme_supports( 'custom-logo' ) && has_custom_logo() ) {
			$logo_id = get_theme_mod( 'custom_logo' );
		}
		if ( $logo_id ) {
			$logo = new Types\ImageObject( null, 'logo' );
			$logo->image_id = $logo_id;

			$organization->add_reference( 'logo', $logo );
			$organization->add_reference( 'image', $logo );
			$manager->add_entity( $logo );
		}

		if ( is_singular() && has_post_thumbnail() ) {
			$thumbnail = new Types\ImageObject( null, 'thumbnail' );
			$thumbnail->image_id = get_post_thumbnail_id();

			$webpage->add_reference( 'primaryImageOfPage', $thumbnail );
			$webpage->add_reference( 'image', $thumbnail );
			$manager->add_entity( $thumbnail );
		}

		if ( is_singular( 'post' ) ) {
			$article = new Types\Article();
			$article->post = get_queried_object();
			$article->add_reference( 'isPartOf', $webpage );
			$article->add_reference( 'mainEntityOfPage', $webpage );
			$manager->add_entity( $article );

			$author = new Types\Person( null, 'author' );
			$author->user = get_userdata( $article->post->post_author );

			$author_image = new Types\ImageObject();
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

			$article->add_reference( 'publisher', $organization );
		}

		if ( is_author() ) {
			$author = new Types\Person( null, 'author' );
			$author->user = get_queried_object();
			$author->add_reference( 'mainEntityOfPage', $webpage );

			$author_image = new Types\ImageObject();
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
}