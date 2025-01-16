<?php
namespace SlimSEO\Schema;

use SlimSEO\Breadcrumbs;
use SlimSEO\MetaTags\CanonicalUrl;
use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Title;
use SlimSEO\Helpers\Images;
use WP_User;

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
		if ( is_singular( [ 'post', 'page' ] ) ) {
			$read_action = new Types\ReadAction( null, $this->canonical_url->get_url() );
			$webpage->add_reference( 'potentialAction', $read_action );
		}

		$organization = new Types\Organization( null, home_url( '/' ) );
		if ( is_front_page() ) {
			$webpage->add_reference( 'about', $organization );
		}
		$this->add_entity( $webpage );

		$website->add_reference( 'publisher', $organization );
		$this->add_entity( $organization );

		$this->add_logo_schema();

		if ( is_singular() ) {
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
		if ( empty( $logo_id ) || ! is_int( $logo_id ) ) {
			return;
		}
		$logo = new Types\ImageObject( 'logo', home_url( '/' ) );
		$logo->set_image_id( $logo_id );

		$this->entities['organization']->add_reference( 'logo', $logo );
		$this->entities['organization']->add_reference( 'image', $logo );
		$this->add_entity( $logo );
	}

	private function add_thumbnail_schema() {
		$post   = get_queried_object();
		$images = Images::get_post_images( $post );
		if ( empty( $images ) ) {
			return;
		}

		$thumbnail   = new Types\ImageObject( 'thumbnail', get_permalink( $post ) );
		$first_image = reset( $images );
		if ( is_numeric( $first_image ) ) {
			$thumbnail->set_image_id( (int) $first_image );
		} else {
			$thumbnail->set_image_url( $first_image );
		}

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

		$author      = new Types\Person( 'author' );
		$author_user = get_userdata( $post->post_author );

		if ( ! ( $author_user instanceof WP_User ) ) {
			return;
		}

		$author->set_user( $author_user );
		$this->add_entity( $author );
		$article->add_reference( 'author', $author );
	}

	private function add_author_schemas() {
		$author      = new Types\Person( 'author' );
		$author_user = get_queried_object();
		if ( ! ( $author_user instanceof WP_User ) ) {
			return;
		}
		$author->set_user( $author_user );
		$this->entities['webpage']->add_reference( 'mainEntity', $author );

		$this->add_entity( $author );
	}

	private function add_entity( $entity ) {
		$this->entities[ $entity->context ] = $entity;
	}
}
