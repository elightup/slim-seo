<?php
namespace SlimSEO\MetaTags;

use WP_Post;
use WP_Post_Type;
use SlimSEO\Helpers\Arr;

class Data {
	private $data = [];

	public function collect( $id = null ): array {
		$this->data = array_merge(
			[ 'post' => $this->get_post_data( $id ) ],
			[ 'post_type' => $this->get_post_type_data() ],
			[ 'term' => $this->get_term_data( $id ) ],
			[ 'author' => $this->get_author_data() ],
			[ 'user' => $this->get_user_data() ],
			[ 'site' => $this->get_site_data() ],
			$this->get_other_data(),
		);
		$this->data = apply_filters( 'slim_seo_data', $this->data );

		// Truncate the post content and set word count.
		$post_content = Helper::normalize( Arr::get( $this->data, 'post.content', '' ) );
		Arr::set( $this->data, 'post.content', $post_content );
		Arr::set( $this->data, 'post.word_count', str_word_count( $post_content ) );

		return $this->data;
	}

	private function get_post_data( $id = null ): array {
		if ( $id ) {
			$post = get_post( $id );
		} else {
			$post = is_singular() ? get_queried_object() : get_post();
		}

		if ( empty( $post ) ) {
			return [];
		}
		$post_content = apply_filters( 'slim_seo_meta_description_generated', $post->post_content, $post );

		$post_tax   = [];
		$taxonomies = Helper::get_taxonomies();
		unset( $taxonomies['category'], $taxonomies['post_tag'] );
		foreach ( $taxonomies as $taxonomy ) {
			$post_tax[ $this->normalize( $taxonomy['slug'] ) ] = $this->get_post_terms( $post, $taxonomy['slug'] );
		}

		return [
			'title'            => $post->post_title,
			'excerpt'          => $post->post_excerpt,
			'content'          => $post_content,
			'auto_description' => $this->generate_auto_description( $id, $post->post_excerpt, $post_content ),
			'date'             => wp_date( get_option( 'date_format' ), strtotime( $post->post_date_gmt ) ),
			'modified_date'    => wp_date( get_option( 'date_format' ), strtotime( $post->post_modified_gmt ) ),
			'thumbnail'        => get_the_post_thumbnail_url( $post->ID, 'full' ),
			'tags'             => $this->get_post_terms( $post, 'post_tag' ),
			'categories'       => $this->get_post_terms( $post, 'category' ),
			'custom_field'     => $this->get_custom_field_data( $post ),
			'tax'              => $post_tax,
		];
	}

	private function get_post_type_data(): array {
		if ( ! is_post_type_archive() ) {
			return [];
		}
		$post_type_object = get_queried_object();
		if ( ! ( $post_type_object instanceof WP_Post_Type ) ) {
			return [];
		}

		$labels = get_post_type_labels( $post_type_object );

		return [
			'labels' => [
				'singular' => $labels->singular_name,
				'plural'   => $labels->name,
			],
		];
	}

	private function get_term_data( $id = null ): array {
		if ( $id ) {
			$term = get_term( $id );
		} else {
			$term = get_queried_object();
		}

		if ( empty( $term ) || ( ! $id && ! ( is_category() || is_tag() || is_tax() ) ) ) {
			return [];
		}

		return [
			'name'             => $term->name,
			'description'      => $term->description,
			'auto_description' => $this->generate_auto_description( $id, $term->description ),
		];
	}

	private function get_user_data() {
		return $this->get_user( get_current_user_id() );
	}

	private function get_author_data() {
		return $this->get_user( get_the_author_meta( 'ID' ) );
	}

	private function get_user( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return [];
		}
		return [
			'display_name' => $user->display_name,
			'description'  => $user->description,
		];
	}

	private function get_site_data() {
		return [
			'title'       => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
		];
	}

	private function get_post_terms( $post, $taxonomy ) {
		$terms = get_the_terms( $post, $taxonomy );
		return is_wp_error( $terms ) ? [] : wp_list_pluck( $terms, 'name' );
	}

	private function get_custom_field_data( WP_Post $post ): array {
		$meta_values = get_post_meta( $post->ID );
		$data        = [];
		foreach ( $meta_values as $key => $value ) {
			$data[ $key ] = reset( $value );
		}
		return $data;
	}

	private function get_other_data(): array {
		global $wp_query, $page, $paged;

		return [
			'current' => [
				'year' => wp_date( 'Y' ),
			],
			'page'    => $paged >= 2 || $page >= 2 ? sprintf( __( 'Page %s', 'slim-seo' ), max( $paged, $page ) ) : '',
			'sep'     => apply_filters( 'document_title_separator', '-' ),
		];
	}

	private function generate_auto_description( ?int $id, string $description, string $content = null ): string {
		$result = $description ?: $content;
		$result = Helper::render( $result, $id );
		return mb_substr( $result, 0, 160 );
	}

	private function normalize( $key ) {
		return str_replace( '-', '_', $key );
	}
}
