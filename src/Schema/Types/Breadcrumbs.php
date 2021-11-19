<?php
namespace SlimSEO\Schema\Types;

class Breadcrumbs extends Base {
	public $source;

	public function is_active() {
		if ( ! parent::is_active() ) {
			return false;
		}

		$this->source->parse();
		$links = $this->source->get_links();
		return ! empty( $links );
	}

	public function generate() {
		$links = $this->source->get_links();
		$list  = array();
		foreach ( $links as $i => $link ) {
			$list[] = array(
				'@type'    => 'ListItem',
				'position' => ( $i + 1 ),
				'name'     => $link['text'],
				'item'     => $link['url'],
			);
		}

		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $this->id,
			'itemListElement' => $list,
		);
	}
}
