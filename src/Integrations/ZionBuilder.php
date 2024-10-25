<?php
namespace SlimSEO\Integrations;

use WP_Post;

class ZionBuilder {
	public function is_active(): bool {
		return class_exists( '\ZionBuilder\Plugin' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_post_content', [ $this, 'filter_content' ], 10, 2 );
	}

	public function filter_content( string $post_content, WP_Post $post ): string {
		return $this->get_builder_content( $post ) ?? $post_content;
	}

	private function get_builder_content( WP_Post $post ): ?string {
		$post_instance = \ZionBuilder\Plugin::$instance->post_manager->get_post_instance( $post->ID );

		if ( ! $post_instance || $post_instance->is_password_protected() || ! $post_instance->is_built_with_zion() ) {
			return null;
		}

		$data = $post_instance->get_template_data();
		return $this->get_elements_content( $data );
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
