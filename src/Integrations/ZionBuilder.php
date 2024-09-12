<?php
namespace SlimSEO\Integrations;

use WP_Post;
use SlimSEO\Helpers\Arr;

class ZionBuilder {
	private $is_auto_genereted = false;

	public function is_active(): bool {
		return class_exists( '\ZionBuilder\Plugin' );
	}

	public function setup() {
		add_filter( 'slim_seo_data', [ $this, 'replace_post_content' ] );
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );
	}

	public function replace_post_content( array $data ): array {
		if ( $this->is_auto_genereted ) {
			return $data;
		}

		$post = is_singular() ? get_queried_object() : get_post();
		if ( empty( $post ) ) {
			return $data;
		}

		$content = $this->get_post_content( $post );
		if ( $content ) {
			Arr::set( $data, 'post.content', $content );
		}

		return $data;
	}

	public function description( $description, WP_Post $post ) {
		$content = $this->get_post_content( $post );
		if ( $content ) {
			$this->is_auto_genereted = true;
			return $content;
		}

		return $description;
	}

	private function get_post_content( WP_Post $post ): string {
		$post_instance = \ZionBuilder\Plugin::$instance->post_manager->get_post_instance( $post->ID );

		if ( ! $post_instance || $post_instance->is_password_protected() || ! $post_instance->is_built_with_zion() ) {
			return '';
		}

		$post_template_data = $post_instance->get_template_data();
		if ( empty( $post_template_data ) ) {
			return '';
		}

		return $this->get_elements_content( $post_template_data );
	}

	private function get_elements_content( array $data ): string {
		$content = array_map( [ $this, 'get_element_content' ], $data );
		return implode( ' ', $content );
	}

	private function get_element_content( array $element ): string {
		$content = empty( $element['content'] ) ? '' : $element['content'];

		if ( is_array( $content ) ) {
			$content = $this->get_elements_content( $content );
		}

		if ( ! empty( $element['options'] ) ) {
			$content .= ' ' . $this->get_element_content( $element['options'] );
		}

		return $content;
	}

	public function remove_post_types( array $post_types ): array {
		$unsupported = [
			'zion_template',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}
}
