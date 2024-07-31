<?php
namespace SlimSEO\Integrations;

use WP_Post;
use SlimSEO\Helpers\Arr;

class Oxygen {
	private $is_auto_genereted = false;

	public function is_active(): bool {
		return defined( 'CT_VERSION' );
	}

	public function setup() {
		add_filter( 'slim_seo_data', [ $this, 'replace_post_content' ] );
		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
	}

	public function replace_post_content( array $data ): array {
		if ( $this->is_auto_genereted ) {
			return $data;
		}

		$post = is_singular() ? get_queried_object() : get_post();
		if ( empty( $post ) ) {
			return $data;
		}
		$content = Arr::get( $data, 'post.content', '' );
		Arr::set( $data, 'post.content', $this->get_content( $content, $post ) );

		return $data;
	}

	public function description( $description, WP_Post $post ) {
		return $this->get_content( $description, $post );
	}

	public function get_content( $content, $post ) {
		// In builder mode.
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			return $content;
		}
		$shortcode = get_post_meta( $post->ID, 'ct_builder_shortcodes', true );

		$this->is_auto_genereted = true;
		return $shortcode ?: $content;
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
