<?php
namespace SlimSEO\Schema\Types;

class BreadcrumbList extends Base {
	public $source;

	public function is_active() {
		if ( ! parent::is_active() ) {
			return false;
		}

		$this->source->setup_args();
		$this->source->parse();
		$links = $this->source->get_links();
		return ! empty( $links );
	}

	public function generate() {
		$links    = $this->source->get_links();
		$list     = [];
		$position = 1;
		foreach ( $links as $link ) {
			$list[] = [
				'@type'    => 'ListItem',
				'position' => $position,
				'name'     => $link['text'],
				'item'     => $link['url'],
			];
			$position++;
		}

		$current_page = $this->source->get_current_page();
		if ( $current_page ) {
			$list[] = [
				'@type'    => 'ListItem',
				'position' => $position,
				'name'     => $current_page,
			];
		}

		return [
			'@type'           => 'BreadcrumbList',
			'name'            => __( 'Breadcrumbs', 'slim-seo' ),
			'@id'             => $this->id,
			'itemListElement' => $list,
		];
	}
}
