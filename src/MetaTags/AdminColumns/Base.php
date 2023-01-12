<?php
namespace SlimSEO\MetaTags\AdminColumns;

use SlimSEO\MetaTags\Settings\Base as Settings;
use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Robots;

abstract class Base {
	protected $settings;
	protected $title;
	protected $description;

	public function __construct( Settings $settings, Title $title, Description $description, Robots $robots ) {
		$this->settings    = $settings;
		$this->title       = $title;
		$this->description = $description;
		$this->robots      = $robots;
	}

	public function columns( $columns ) {
		$columns['meta_title']       = esc_html__( 'Meta title', 'slim-seo' );
		$columns['meta_description'] = esc_html__( 'Meta desc.', 'slim-seo' );
		$columns['noindex']          = esc_html__( 'Index', 'slim-seo' );

		return $columns;
	}
}
