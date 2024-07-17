<?php
namespace SlimSEO\MetaTags;

use SlimSEO\Helpers\Arr;
use SlimSEO\Helpers\Images;

class Data {
	private $data = [];

	public function collect(): array {
		$this->data = array_merge(
			[ 'post'   => $this->get_post_data() ],
			[ 'term'   => $this->get_term_data() ],
			[ 'author' => $this->get_author_data() ],
			[ 'user'   => $this->get_user_data() ],
			[ 'site'   => $this->get_site_data() ],
			[ 'other'  => $this->get_other_data() ],
		);

		// Truncate the post content and set word count.
		$post_content = Helper::normalize( Arr::get( $this->data, 'post.content', '' ) );
		Arr::set( $this->data, 'post.content', $post_content );
		Arr::set( $this->data, 'post.word_count', str_word_count( $post_content ) );

		return $this->data;
	}

	private function get_post_data(): array {
		$post = is_singular() ? get_queried_object() : get_post();
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
			'date'          => gmdate( 'c', strtotime( $post->post_date_gmt ) ),
			'modified_date' => gmdate( 'c', strtotime( $post->post_modified_gmt ) ),
			'thumbnail'     => get_the_post_thumbnail_url( $post->ID, 'full' ),
			'comment_count' => (int) $post->comment_count,
			'tags'          => $this->get_post_terms( $post, 'post_tag' ),
			'categories'    => $this->get_post_terms( $post, 'category' ),
			'custom_field'  => $this->get_custom_field_data(),
			'tax'           => $post_tax,
		];
	}

	private function get_term_data(): array {
		$term = get_queried_object();

		if ( ! ( is_category() || is_tag() || is_tax() ) || empty( $term ) ) {
			return [];
		}

		return [
			'ID'	      => $term->term_id,
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

	private function get_custom_field_data() {
		$post        = is_singular() ? get_queried_object() : get_post();
		$meta_values = get_post_meta( $post->ID );
		$data        = [];
		foreach ( $meta_values as $key => $value ) {
			$data[ $key ] = reset( $value );
		}
		return $data;
	}

	private function get_other_data(): array {
		global $wp, $wp_query;

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$url = $_SERVER['REQUEST_URI'] ?? add_query_arg( [], $wp->request );
		$url = home_url( $url );
		$url = esc_url( wp_strip_all_tags( $url ) );
		$url = strtok( $url, '#' );
		$url = strtok( $url, '?' );
		$date = new \DateTime();


		return [
			'url'   => $url,
			// 'title' => wp_get_document_title(),
			'year'      => current_datetime()->format('Y-m-d H:i:s'),
			'month'     => current_datetime()->format('m'),
			'date'      => current_datetime()->format('d'),
			'day'       => $date->format( 'j' ),
			'time'      => current_time( 'timestamp', true ),
			'filename'  => $this->get_attr_name(),
			'page'      => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
			'pagetotal' => $wp_query->max_num_pages,
			'separator' => '-',
			
		];
	}

	private function get_attr_name() {
		$images = Images::get_post_images( get_queried_object() );
		if ( empty( $images ) ) {
			return '';
		}

		$image_id = reset( $images );
		if ( ! is_numeric( $image_id ) ) {
			$image_id = Images::get_id_from_url( $image_id );
		}

		return basename( get_attached_file( $image_id ) );
	}

	private function normalize( $key ) {
		return str_replace( '-', '_', $key );
	}
}