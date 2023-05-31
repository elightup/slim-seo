<?php
namespace SlimSEO\Helpers;

class Data {
	public static function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		unset( $post_types['attachment'] );
		$post_types = apply_filters( 'slim_seo_post_types', $post_types );

		return $post_types;
	}

	public static function get_taxonomies() {
		$taxonomies = get_taxonomies( [
			'public'  => true,
			'show_ui' => true,
		], 'objects' );
		unset( $taxonomies['post_format'] );
		$taxonomies = apply_filters( 'slim_seo_taxonomies', $taxonomies );

		return $taxonomies;
	}

	public static function get_post_type_archive_page( string $post_type ) {
		$post_type_object = get_post_type_object( $post_type );
		if ( ! is_string( $post_type_object->has_archive ) ) {
			return null;
		}

		$page = get_page_by_path( $post_type_object->has_archive );
		return $page ?: null;
	}
}
