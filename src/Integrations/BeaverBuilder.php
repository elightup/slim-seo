<?php
namespace SlimSEO\Integrations;

class BeaverBuilder {
	public function __construct() {
		add_filter( 'fl_builder_disable_schema', '__return_true' );
		add_filter( 'fl_theme_disable_schema', '__return_true' );
	}
}