<?php
namespace SlimSEO\Schema\Entities;

class Website extends Base {
	public function get_schema() {
		$schema = [
			'@type'    => 'WebSite',
			'@id'      => $this->id,
			'url'      => $this->url,
			'name'     => get_bloginfo( 'name' ),
		];

		if ( false === apply_filters( 'slim_seo_schema_search_box_enable', true ) ) {
			return $schema;
		}

		$search_box = $this->get_search_box_schema();
		$search_box = apply_filters( 'slim_seo_schema_search_box', $search_box );

		$schema['potentialAction'] = $search_box;

		return $schema;
	}

	/*
	 * Get sitelinks search box.
	 * @see https://developers.google.com/search/docs/data-types/sitelinks-searchbox
	 */
	private function get_search_box_schema() {
		return [
			'@type'       => 'SearchAction',
			'target'      => esc_url( home_url( '/' ) ) . '?s={search_term_string}',
			'query-input' => 'required name=search_term_string',
		];
	}
}
