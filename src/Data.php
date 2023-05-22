<?php
namespace SlimSEO;

class Data {
	public static function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		unset( $post_types['attachment'] );
		$post_types = apply_filters( 'slim_seo_post_types', $post_types );

		return $post_types;
	}

	public static function get_taxonomies() {
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		unset( $taxonomies['post_format'] );
		$taxonomies = apply_filters( 'slim_seo_taxonomies', $taxonomies );

		return $taxonomies;
	}
}
