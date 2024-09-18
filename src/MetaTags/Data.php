<?php
namespace SlimSEO\MetaTags;

use WP_Post;
use SlimSEO\Helpers\Arr;

class Data {
	private $data = [];

	public function collect( $id = null ): array {
		$this->data = array_merge(
			[ 'post' => $this->get_post_data( $id ) ],
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

		$post_tax   = [];
		$taxonomies = Helper::get_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			$post_tax[ $this->normalize( $taxonomy['slug'] ) ] = $this->get_post_terms( $post, $taxonomy['slug'] );
		}

		return [
			'ID'            => $post->ID,
			'title'         => $post->post_title,
			'excerpt'       => $post->post_excerpt,
			'content'       => $post->post_content,
			'url'           => get_permalink( $post ),
			'slug'          => $post->post_name,
			'date'          => wp_date( get_option( 'date_format' ), strtotime( $post->post_date_gmt ) ),
			'modified_date' => wp_date( get_option( 'date_format' ), strtotime( $post->post_modified_gmt ) ),
			'thumbnail'     => get_the_post_thumbnail_url( $post->ID, 'full' ),
			'comment_count' => (int) $post->comment_count,
			'tags'          => $this->get_post_terms( $post, 'post_tag' ),
			'categories'    => $this->get_post_terms( $post, 'category' ),
			'custom_field'  => $this->get_custom_field_data( $post ),
			'tax'           => $post_tax,
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
			'ID'          => $term->term_id,
			'name'        => $term->name,
			'slug'        => $term->slug,
			'taxonomy'    => $term->taxonomy,
			'description' => $term->description,
			'url'         => get_term_link( $term->term_id ),
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
			'ID'           => $user->ID,
			'first_name'   => $user->first_name,
			'last_name'    => $user->last_name,
			'display_name' => $user->display_name,
			'login'        => $user->user_login,
			'nickname'     => $user->nickname,
			'email'        => $user->user_email,
			'url'          => $user->user_url,
			'nicename'     => $user->user_nicename,
			'description'  => $user->description,
			'posts_url'    => get_author_posts_url( $user->ID ),
			'avatar'       => get_avatar_url( $user->ID ),
		];
	}

	private function get_site_data() {
		return [
			'title'       => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'url'         => home_url( '/' ),
			'language'    => get_locale(),
			'icon'        => get_site_icon_url(),
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
		global $wp_query;

		return [
			'current' => [
				'year'     => wp_date( 'Y' ),
				'month'    => wp_date( 'm' ),
				'day'      => wp_date( 'j' ),
				'date'     => wp_date( get_option( 'date_format' ) ),
				'time'     => wp_date( get_option( 'time_format' ) ),
			],
			'pagination' => [
				'page'  => max( get_query_var( 'paged' ), 1 ),
				'total' => $wp_query->max_num_pages,
			],
			'separator'        => apply_filters( 'document_title_separator', '-' ),
		];
	}

	private function normalize( $key ) {
		return str_replace( '-', '_', $key );
	}
}
