<?php
namespace SlimSEO\Integrations;

class MyListing {
	public function is_active(): bool {
		return get_template() === 'my-listing';
	}

	public function setup() {
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_taxonomies', [ $this, 'remove_taxonomies' ] );
	}

	public function remove_post_types( array $post_types ): array {
		$unsupported = [
			'job_listing',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}

	public function remove_taxonomies( array $taxonomies ): array {
		$unsupported = [
			'region',
		];
		return array_diff_key( $taxonomies, array_flip( $unsupported ) );
	}
}
