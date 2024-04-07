<?php
namespace SlimSEO\Schema;

use SlimSEO\Breadcrumbs;
use SlimSEO\MetaTags\CanonicalUrl;
use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Title;

class Manager {
	private $title;
	private $description;
	private $breadcrumbs;
	private $canonical_url;
	private $entities = [];

	public function __construct( Title $title, Description $description, Breadcrumbs $breadcrumbs, CanonicalUrl $canonical_url ) {
		$this->title         = $title;
		$this->description   = $description;
		$this->breadcrumbs   = $breadcrumbs;
		$this->canonical_url = $canonical_url;
	}

	public function setup() {
		add_action( 'wp_footer', [ $this, 'output' ] );
	}

	public function output() {
		$this->add_schemas();

		$entities = apply_filters( 'slim_seo_schema_entities', $this->entities );
		$entities = array_filter( $entities, function ( $entity ) {
			return $entity->is_active();
		} );

		$graph = array_map( function ( $entity ) {
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
		$flags  = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
		if ( wp_get_environment_type() !== 'production' ) {
			$flags = $flags | JSON_PRETTY_PRINT;
		}
		echo '<script type="application/ld+json" id="slim-seo-schema">', wp_json_encode( $schema, $flags ), '</script>';
	}

	private function add_schemas() {
		$website = new Types\Website( null, home_url( '/' ) );
		$this->add_entity( $website );

		$search_action = new Types\SearchAction( null, home_url( '/' ) );
		$website->add_reference( 'potentialAction', $search_action );
		$this->add_entity( $search_action );

		$breadcrumb_list         = new Types\BreadcrumbList( null, $this->canonical_url->get_url() );
		$breadcrumb_list->source = $this->breadcrumbs;
		$this->add_entity( $breadcrumb_list );

		$webpage              = new Types\WebPage( null, $this->canonical_url->get_url() );
		$webpage->title       = $this->title;
		$webpage->description = $this->description;
		$webpage->add_reference( 'isPartOf', $website );
		$webpage->add_reference( 'breadcrumb', $breadcrumb_list );
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
		$logo           = new Types\ImageObject( 'logo', home_url( '/' ) );
		$logo->image_id = $logo_id;

		$this->entities['organization']->add_reference( 'logo', $logo );
		$this->entities['organization']->add_reference( 'image', $logo );
		$this->add_entity( $logo );
	}

	private function add_thumbnail_schema() {
		$post                = get_queried_object();
		$thumbnail           = new Types\ImageObject( 'thumbnail', get_permalink( $post ) );
		$thumbnail->image_id = get_post_thumbnail_id( $post );

		$this->entities['webpage']->add_reference( 'primaryImageOfPage', $thumbnail );
		$this->entities['webpage']->add_reference( 'image', $thumbnail );
		$this->add_entity( $thumbnail );
	}

	private function add_post_schemas() {
		$post    = get_queried_object();
		$article = new Types\Article( null, get_permalink( $post ) );
		$article->add_reference( 'isPartOf', $this->entities['webpage'] );
		$article->add_reference( 'mainEntityOfPage', $this->entities['webpage'] );
		$this->add_entity( $article );

		if ( isset( $this->entities['thumbnail'] ) ) {
			$article->add_reference( 'image', $this->entities['thumbnail'] );
		}

		$article->add_reference( 'publisher', $this->entities['organization'] );

		$author       = new Types\Person( 'author', get_author_posts_url( $post->post_author ) );
		$author->user = get_userdata( $post->post_author );

		if ( ! $author->user ) {
			return;
		}

		$this->add_entity( $author );
		$article->add_reference( 'author', $author );
	}

	private function add_author_schemas() {
		$author       = new Types\Person( 'author', get_author_posts_url( get_queried_object_id() ) );
		$author->user = get_queried_object();
		$author->add_reference( 'mainEntityOfPage', $this->entities['webpage'] );

		$this->add_entity( $author );
	}

	private function add_entity( $entity ) {
		$this->entities[ $entity->context ] = $entity;
	}
}
