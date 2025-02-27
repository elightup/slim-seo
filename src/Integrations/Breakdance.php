<?php
namespace SlimSEO\Integrations;

use WP_Post;

class Breakdance {
	public function is_active(): bool {
		return defined( '__BREAKDANCE_VERSION' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_post_content', [ $this, 'filter_content' ], 10, 2 );
	}

	public function filter_content( string $post_content, WP_Post $post ): string {
		return $this->get_builder_content( $post ) ?? $post_content;
	}

	private function get_builder_content( WP_Post $post ): ?string {
		return \Breakdance\Admin\get_mode( $post->ID ) === 'breakdance' ? \Breakdance\Data\get_tree_as_html( $post->ID ) : null;
	}
}
