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
	}

	public function output() {
		$this->add_schemas();

		$entities = apply_filters( 'slim_seo_schema_entities', $this->entities );
		$entities = array_filter( $entities, function( $entity ) {
			return $entity->is_active();
		} );

		$graph = array_map( function( $entity ) {
			return $entity->get_schema();
		}, $entities );

		$graph = apply_filters( 'slim_seo_schema_graph', $graph );

		$graph = array_values( array_filter( $graph ) );
		if ( empty( $graph ) ) {
			return;
		}

		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		];
		echo '<script type="application/ld+json">', wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), '</script>';
	}

	private function add_schemas() {
		$website = new Types\Website( null, home_url( '/' ) );
		$this->add_entity( $website );

		$search_action = new Types\SearchAction;
		$website->add_reference( 'potentialAction', $search_action );
		$this->add_entity( $search_action );

		$breadcrumbs         = new Types\Breadcrumbs;
		$breadcrumbs->source = $this->breadcrumbs;
		$this->add_entity( $breadcrumbs );

		$webpage              = new Types\WebPage;
		$webpage->title       = $this->title;
		$webpage->description = $this->description;
		$webpage->add_reference( 'isPartOf', $website );
		$webpage->add_reference( 'breadcrumb', $breadcrumbs );
		$this->add_entity( $webpage );

		$organization = new Types\Organization( null, home_url( '/' ) );
		$website->add_reference( 'publisher', $organization );
		$this->add_entity( $organization );

		$this->add_logo_schema();

		if ( is_singular() && has_post_thumbnail() ) {
			$this->add_thumbnail_schema();
		}
		if ( is_singular( 'post' ) ) {
			$this->add_post_schemas();
		}
		if ( is_author() ) {
			$this->add_author_schemas();
		}
	}

	private function add_logo_schema() {
		$logo_id = get_option( 'site_icon' );
		if ( current_theme_supports( 'custom-logo' ) && has_custom_logo() ) {
			$logo_id = get_theme_mod( 'custom_logo' );
		}
		if ( ! $logo_id ) {
			return;
		}
		$logo           = new Types\ImageObject( 'logo' );
		$logo->image_id = $logo_id;

		$this->entities['organization']->add_reference( 'logo', $logo );
		$this->entities['organization']->add_reference( 'image', $logo );
		$this->add_entity( $logo );
	}

	private function add_thumbnail_schema() {
		$thumbnail           = new Types\ImageObject( 'thumbnail' );
		$thumbnail->image_id = get_post_thumbnail_id();

		$this->entities['webpage']->add_reference( 'primaryImageOfPage', $thumbnail );
		$this->entities['webpage']->add_reference( 'image', $thumbnail );
		$this->add_entity( $thumbnail );
	}

	private function add_post_schemas() {
		$article = new Types\Article;
		$article->add_reference( 'isPartOf', $this->entities['webpage'] );
		$article->add_reference( 'mainEntityOfPage', $this->entities['webpage'] );
		$this->add_entity( $article );

		if ( isset( $this->entities['thumbnail'] ) ) {
			$article->add_reference( 'image', $this->entities['thumbnail'] );
		}

		$article->add_reference( 'publisher', $this->entities['organization'] );

		$author       = new Types\Person( 'author' );
		$author->user = get_userdata( get_queried_object()->post_author );

		if ( ! $author->user ) {
			return;
		}
		$author_image = $this->get_author_image_schema( $author->user->ID );
		$author->add_reference( 'image', $author_image );

		$this->add_entity( $author );
		$this->add_entity( $author_image );

		$article->add_reference( 'author', $author );
	}

	private function add_author_schemas() {
		$author       = new Types\Person( 'author' );
		$author->user = get_queried_object();
		$author->add_reference( 'mainEntityOfPage', $this->entities['webpage'] );

		$author_image = $this->get_author_image_schema( $author->user->ID );
		$author->add_reference( 'image', $author_image );

		$this->add_entity( $author );
		$this->add_entity( $author_image );
	}

	private function get_author_image_schema( $user_id ) {
		$user         = get_userdata( $user_id );
		$author_image = new Types\ImageObject( 'author_image' );
		$author_image->add_property( 'url', get_avatar_url( $user_id ) );
		$author_image->add_property( 'width', 96 );
		$author_image->add_property( 'height', 96 );
		$author_image->add_property( 'caption', $user->display_name );

		return $author_image;
	}

	private function add_entity( $entity ) {
		$this->entities[ $entity->context ] = $entity;
	}
}
