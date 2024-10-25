<?php
namespace SlimSEO\Integrations;

use WP_Post;

class Oxygen {
	public function is_active(): bool {
		return defined( 'CT_VERSION' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_post_content', [ $this, 'filter_content' ], 10, 2 );
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
	}

	public function filter_content( string $post_content, WP_Post $post ): string {
		return $this->get_builder_content( $post ) ?: $post_content;
	}

	public function get_builder_content( WP_Post $post ): string {
		// In builder mode.
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			return '';
		}

		return (string) get_post_meta( $post->ID, 'ct_builder_shortcodes', true );
	}

	public function skip_shortcodes( $shortcodes ) {
		$shortcodes[] = 'ct_slider';
		$shortcodes[] = 'ct_code_block';
		$shortcodes[] = 'oxy-form_widget';
		return $shortcodes;
	}

	public function remove_post_types( $post_types ) {
		unset( $post_types['ct_template'] );
		unset( $post_types['oxy_user_library'] );
		return $post_types;
	}
}
