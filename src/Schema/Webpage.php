<?php
namespace SlimSEO\Schema;

class Webpage extends Type {
	private $title;
	private $description;

	public function __construct( $title, $description ) {
		$this->title       = $title;
		$this->description = $description;
	}

	public function get_schema() {
		$schema = [
			'@type'       => 'WebPage',
			'@id'         => $this->get_id(),
			'url'         => $this->get_url(),
			'inLanguage'  => get_locale(),
			'name'        => $this->title->get_title(),
			'description' => $this->description->get_description(),
			'breadcrumb'  => [
				'@id' => $this->get_id( 'breadcrumbs' ),
			],
			'isPartOf'    => [
				'@id' => $this->get_id( 'website', true ),
			],
		];

		if ( is_singular() ) {
			$schema['datePublished'] = get_the_time( 'c' );
			$schema['dateModified']  = get_the_modified_time( 'c' );
			$schema['primaryImageOfPage'] = $this->get_thumbnail_schema();
			$schema['image'] = [
				'@id' => $this->get_id( 'imageobject' ),
			];
		}

		return $schema;
	}

	private function get_thumbnail_schema() {
		$thumbnail_id = get_post_thumbnail_id();
		if ( ! $thumbnail_id ) {
			return null;
		}
		$thumbnail = new ImageObject( $thumbnail_id );

		return $thumbnail->get_schema();
	}
}
