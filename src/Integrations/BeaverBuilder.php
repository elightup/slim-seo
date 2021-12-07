<?php
namespace SlimSEO\Integrations;

class BeaverBuilder {
	public function setup() {
		add_filter( 'fl_builder_disable_schema', '__return_true' );
		add_filter( 'fl_theme_disable_schema', '__return_true' );

		add_filter( 'slim_seo_meta_box_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_sitemap_post_types', [ $this, 'remove_post_types' ] );
	}

	public function remove_post_types( $post_types ) {
		unset( $post_types['fl-builder-template'] );
		return $post_types;
	}
}
