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
		add_filter( 'slim_seo_meta_description_generated', [ $this, 'description' ], 10, 2 );
	}

	public function replace_post_content( $data ) {
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
		$post_instance = \ZionBuilder\Plugin::$instance->post_manager->get_post_instance( $post->ID );

		if ( ! $post_instance || $post_instance->is_password_protected() || ! $post_instance->is_built_with_zion() ) {
			return $content;
		}

		$post_template_data = $post_instance->get_template_data();
		if ( empty( $post_template_data ) ) {
			return $content;
		}

		$content = $this->get_content_detail( $post_template_data );

		$this->is_auto_genereted = true;
		return $content;
	}

	private function get_content_detail( $data ) {
		$content = array_map( [ $this, 'get_element_content' ], $data );
		return implode( ' ', $content );
	}

	private function get_element_content( $element ) {
		$content = empty( $element['content'] ) ? '' : $element['content'];

		if ( is_array( $content ) ) {
			$content = $this->get_content_detail( $content );
		}

		if ( ! empty( $element['options'] ) ) {
			$content .= ' ' . $this->get_element_content( $element['options'] );
		}

		return $content;
	}
}
