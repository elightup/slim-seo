<?php
namespace SlimSEO\Schema\Entities;

class Webpage extends Base {
	protected $title;
	protected $description;

	public function get_schema() {
		$schema = [
			'@type'       => 'WebPage',
			'@id'         => $this->id,
			'url'         => $this->url,
			'inLanguage'  => get_locale(),
			'name'        => $this->title->get_title(),
			'description' => $this->description->get_description(),
			'isPartOf'    => [
				'@id' => $this->parent->id,
			],
		];
		$schema = array_merge( $schema, $this->children );

		if ( is_singular() ) {
			$schema['datePublished'] = date( 'c', strtotime( get_queried_object()->post_date_gmt ) );
			$schema['dateModified']  = date( 'c', strtotime( get_queried_object()->post_modified_gmt ) );

			$thumbnail = $this->get_thumbnail();
			if ( null !== $thumbnail ) {
				$schema['primaryImageOfPage'] = $thumbnail->get_schema();
				$schema['image'] = [
					'@id' => $thumbnail->id,
				];
			}
		}

		return $schema;
	}

	private function get_thumbnail() {
		$thumbnail_id = get_post_thumbnail_id();
		if ( ! $thumbnail_id ) {
			return null;
		}
		$thumbnail = new ImageObject( null, 'primaryimage' );
		$thumbnail->image_id = $thumbnail_id;

		return $thumbnail;
	}
}
