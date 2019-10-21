<?php
namespace SlimSEO\MetaTags\Settings;

class Post extends Base {
	protected $object_type = 'post';

	public function __construct() {
		add_action( 'admin_print_styles-post.php', [ $this, 'enqueue' ] );
		add_action( 'admin_print_styles-post-new.php', [ $this, 'enqueue' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function add_meta_box() {
		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type ) {
			add_meta_box( 'slim-seo', __( 'Search Engine Optimization', 'slim-seo' ), [ $this, 'render' ], $post_type, 'normal', 'high' );
		}
	}

	private function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ] );
		$post_types = array_diff( $post_types, [ 'attachment' ] );

		return $post_types;
	}

	protected function get_object_id() {
		return get_the_ID();
	}
}