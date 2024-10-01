<?php
namespace SlimSEO\MetaTags\Settings;

use SlimSEO\Helpers\Data;

class Post extends Base {
	public function setup(): void {
		$this->object_type = 'post';
		add_action( 'admin_print_styles-post.php', [ $this, 'enqueue' ] );
		add_action( 'admin_print_styles-post-new.php', [ $this, 'enqueue' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function add_meta_box() {
		$context  = apply_filters( 'slim_seo_meta_box_context', 'normal' );
		$priority = apply_filters( 'slim_seo_meta_box_priority', 'low' );

		$post_types = $this->get_types();
		foreach ( $post_types as $post_type ) {
			add_meta_box( 'slim-seo', __( 'Search Engine Optimization', 'slim-seo' ), [ $this, 'render' ], $post_type, $context, $priority );
		}
	}

	public function get_types() {
		$post_types = array_keys( Data::get_post_types() );
		$post_types = apply_filters( 'slim_seo_meta_box_post_types', $post_types );

		return $post_types;
	}

	protected function get_object_id() {
		return get_the_ID();
	}
}
