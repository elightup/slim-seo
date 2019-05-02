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
		if ( empty( $entities ) ) {
			return;
		}
		$graph = [];
		foreach ( $entities as $context => $entity ) {
			if ( false === apply_filters( "slim_seo_schema_{$context}_enable", true ) ) {
				continue;
			}

			$schema = $entity->get_schema();
			$schema = apply_filters( "slim_seo_schema_{$context}", $schema );
			if ( null === $schema ) {
				continue;
			}

			$graph[] = $schema;
		}
		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		];
		echo "<script type='application/ld+json'>\n", json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n</script>\n";
	}
}
