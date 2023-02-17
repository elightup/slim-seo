<?php
namespace SlimSEO\MetaTags\Settings;

class Post extends Base {
	public function setup() {
		$this->object_type = 'post';
		add_action( 'admin_print_styles-post.php', [ $this, 'enqueue' ] );
		add_action( 'admin_print_styles-post-new.php', [ $this, 'enqueue' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	protected function get_script_params() : array {
		$params = parent::get_script_params();

		// @codingStandardsIgnoreLine.
		$is_home = 'page' === get_option( 'show_on_front' ) && $this->get_object_id() == get_option( 'page_on_front' );
		$params['isHome'] = $is_home;

		if ( $is_home ) {
			$params['title']['parts'] = apply_filters( 'slim_seo_title_parts', [ 'site', 'tagline' ], 'home' );
		}

		return $params;
	}

	public function add_meta_box() {
		$context  = apply_filters( 'slim_seo_meta_box_context', 'normal' );
		$priority = apply_filters( 'slim_seo_meta_box_priority', 'high' );

		$post_types = $this->get_types();
		foreach ( $post_types as $post_type ) {
			add_meta_box( 'slim-seo', __( 'Search Engine Optimization', 'slim-seo' ), [ $this, 'render' ], $post_type, $context, $priority );
		}
	}

	public function get_types() {
		$post_types = get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );
		$post_types = apply_filters( 'slim_seo_meta_box_post_types', $post_types );

		return $post_types;
	}

	protected function get_object_id() {
		return get_the_ID();
	}
}
