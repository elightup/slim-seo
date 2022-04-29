<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Settings\Base as Settings;
use SlimSEO\MetaTags\Title;

abstract class Base {
	protected $settings;
	protected $title;
	protected $description;

	public function __construct( Settings $settings, Title $title, Description $description ) {
		$this->settings    = $settings;
		$this->title       = $title;
		$this->description = $description;
	}

	public function columns( $columns ) {
		$columns['meta_title']       = esc_html__( 'Meta title', 'slim-seo' );
		$columns['meta_description'] = esc_html__( 'Meta description', 'slim-seo' );

		return $columns;
	}
}
