<?php
namespace SlimSEO\Schema;

class Manager {
	private $title;
	private $description;
	private $breadcrumbs;
	private $entities = [];

	public function __construct( $title, $description, $breadcrumbs ) {
		$this->title       = $title;
		$this->description = $description;
		$this->breadcrumbs = $breadcrumbs;
	}

	public function setup() {
		add_action( 'wp_footer', [ $this, 'output' ] );
		add_action( 'amp_post_template_footer', [ $this, 'output' ] );

		add_action( 'amp_post_template_head', array( $this, 'remove_default_amp_schema' ), 9 );
	}

	public function remove_default_amp_schema() {
		remove_action( 'amp_post_template_head', 'amp_print_schemaorg_metadata' );
	}

	public function output() {
		if ( false === apply_filters( 'slim_seo_schema_enable', true ) ) {
			return;
		}

		$this->add_schemas();

		$entities = apply_filters( 'slim_seo_schema_entities', $this->entities );
		$entities = array_filter( $entities, function( $entity ) {
			return $entity->is_active();
		} );

		$graph = array_map( function( $entity ) {
			return $entity->get_schema();
		}, $entities );

		$graph = array_values( array_filter( $graph ) );
		if ( empty( $graph ) ) {
			return;
		}

		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		];
		echo "<script type='application/ld+json'>", json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), "</script>";
	}

	private function add_schemas() {
		$website = new Types\Website( null, home_url( '/' ) );
		$this->add_entity( $website );

		$search_action = new Types\SearchAction;
		$website->add_reference( 'potentialAction', $search_action );
		$this->add_entity( $search_action );

		$breadcrumbs = new Types\Breadcrumbs;
		$breadcrumbs->source = $this->breadcrumbs;
		$this->add_entity( $breadcrumbs );

		$webpage = new Types\WebPage;
		$webpage->title = $this->title;
		$webpage->description = $this->description;
		$webpage->add_reference( 'isPartOf', $website );
		$webpage->add_reference( 'breadcrumb', $breadcrumbs );
		$this->add_entity( $webpage );

		$organization = new Types\Organization( null, home_url( '/' ) );
		$website->add_reference( 'publisher', $organization );
		$this->add_entity( $organization );

		$logo_id = get_option( 'site_icon' );
		if ( current_theme_supports( 'custom-logo' ) && has_custom_logo() ) {
			$logo_id = get_theme_mod( 'custom_logo' );
		}
		if ( $logo_id ) {
			$logo = new Types\ImageObject( 'logo' );
			$logo->image_id = $logo_id;

			$organization->add_reference( 'logo', $logo );
			$organization->add_reference( 'image', $logo );
			$this->add_entity( $logo );
		}

		if ( is_singular() && has_post_thumbnail() ) {
			$thumbnail = new Types\ImageObject( 'thumbnail' );
			$thumbnail->image_id = get_post_thumbnail_id();

			$webpage->add_reference( 'primaryImageOfPage', $thumbnail );
			$webpage->add_reference( 'image', $thumbnail );
			$this->add_entity( $thumbnail );
		}

		if ( is_singular( 'post' ) ) {
			$article = new Types\Article;
			$article->add_reference( 'isPartOf', $webpage );
			$article->add_reference( 'mainEntityOfPage', $webpage );
			$this->add_entity( $article );

			$author = new Types\Person( 'author' );
			$author->user = get_userdata( get_queried_object()->post_author );

			if ( $author->user ) {
				$author_image = new Types\ImageObject( 'author_image' );
				$author_image->add_property( 'url', get_avatar_url( $author->user->ID ) );
				$author_image->add_property( 'width', 96 );
				$author_image->add_property( 'height', 96 );
				$author_image->add_property( 'caption', $author->user->display_name );

				$author->add_reference( 'image', $author_image );

				$this->add_entity( $author );
				$this->add_entity( $author_image );

				$article->add_reference( 'author', $author );
			}

			if ( has_post_thumbnail() ) {
				$article->add_reference( 'image', $thumbnail );
			}

			$article->add_reference( 'publisher', $organization );
		}

		if ( is_author() ) {
			$author = new Types\Person( 'author' );
			$author->user = get_queried_object();
			$author->add_reference( 'mainEntityOfPage', $webpage );

			$author_image = new Types\ImageObject( 'author_image' );
			$author_image->add_property( 'url', get_avatar_url( $author->user->ID ) );
			$author_image->add_property( 'width', 96 );
			$author_image->add_property( 'height', 96 );
			$author_image->add_property( 'caption', $author->user->display_name );

			$author->add_reference( 'image', $author_image );

			$this->add_entity( $author );
			$this->add_entity( $author_image );
		}
	}

	private function add_entity( $entity ) {
		$this->entities[ $entity->context ] = $entity;
	}
}