<?php
namespace SlimSEO\Schema;

class Manager {
	private $entities = [];

	public function add_entity( $entity ) {
		$this->entities[ $entity->context ] = $entity;
	}

	public function output() {
		if ( false === apply_filters( 'slim_seo_schema_enable', true ) ) {
			return;
		}
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
}
