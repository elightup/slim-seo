<?php
namespace SlimSEO\Schema\Types;

class WebPage extends Base {
	public $title;
	public $description;

	public function generate() {
		$schema = [
			'@type'       => 'WebPage',
			'@id'         => $this->id,
			'url'         => $this->url,
			'inLanguage'  => get_locale(),
			'name'        => $this->title->get_title(),
			'description' => $this->description->get_description(),
		];

		if ( is_post_type_archive() || is_tax() || is_category() || is_tag() || is_date() ) {
			$schema['@type'] = 'CollectionPage';
		}

		if ( is_search() ) {
			$schema['@type'] = 'SearchResultsPage';
		}

		if ( is_author() ) {
			$schema['@type'] = 'ProfilePage';
		}

		if ( is_singular() ) {
			$schema['datePublished'] = gmdate( 'c', strtotime( get_queried_object()->post_date_gmt ) );
			$schema['dateModified']  = gmdate( 'c', strtotime( get_queried_object()->post_modified_gmt ) );
		}

		return $schema;
	}
}
