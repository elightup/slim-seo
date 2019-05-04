<?php
namespace SlimSEO\Schema\Types;

class Webpage extends Base {
	protected $title;
	protected $description;

	public function generate_schema() {
		$schema = [
			'@type'       => 'WebPage',
			'@id'         => $this->id,
			'url'         => $this->url,
			'inLanguage'  => get_locale(),
			'name'        => $this->title->get_title(),
			'description' => $this->description->get_description(),
		];

		if ( is_singular() ) {
			$schema['datePublished'] = date( 'c', strtotime( get_queried_object()->post_date_gmt ) );
			$schema['dateModified']  = date( 'c', strtotime( get_queried_object()->post_modified_gmt ) );
		}

		return $schema;
	}
}
