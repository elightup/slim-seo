<?php
namespace SlimSEO\MetaTags\Settings;

class Post extends Base {
	public function __construct() {
		add_action( 'admin_print_styles-post.php', [ $this, 'enqueue' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function add_meta_box() {
		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type ) {
			add_meta_box( 'slim-seo', __( 'SEO', 'slim-seo' ), [ $this, 'render' ], $post_type, 'normal', 'high' );
		}
	}

	private function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ] );
		$post_types = array_diff( $post_types, [ 'attachment' ] );

		return $post_types;
	}

	public function save( $post_id ) {
		$data = $this->get_form_data();
		if ( null !== $data ) {
			update_post_meta( $post_id, 'slim_seo', $data );
		}
	}

	protected function get_data() {
		$data = get_post_meta( get_the_ID(), 'slim_seo', true );
		$data = $data ? $data : [];

		return array_merge( $this->defaults, $data );
	}
}