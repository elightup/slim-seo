<?php
namespace SlimSEO\MetaTags;

use WP_Post;
use WP_Term;
use WP_Post_Type;

class Data {
	private $data = [];

	private $post_id = 0;
	private $term_id = 0;

	public function set_post_id( int $post_id ): void {
		$this->post_id = $post_id;
	}

	public function set_term_id( int $term_id ): void {
		$this->term_id = $term_id;
	}

	public function collect(): array {
		$this->data = array_merge(
			[ 'post' => $this->get_post_data() ],
			[ 'post_type' => $this->get_post_type_data() ],
			[ 'term' => $this->get_term_data() ],
			[ 'author' => $this->get_author_data() ],
			[ 'user' => $this->get_user_data() ],
			[ 'site' => $this->get_site_data() ],
			$this->get_other_data()
		);
		$this->data = apply_filters( 'slim_seo_data', $this->data, $this->post_id, $this->term_id );

		return $this->data;
	}

	private function get_post_data(): array {
		$post = get_post( $this->post_id ?: QueriedObject::get_id() );
		if ( empty( $post ) ) {
			return [];
		}
		$post_content = self::get_post_content( $post->ID );

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
			'auto_description' => Helper::truncate( $post->post_excerpt ?: $post_content ),
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

	private function get_term_data(): array {
		$term = null;

		if ( $this->term_id ) {
			$term = get_term( $this->term_id );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
		}

		if ( ! ( $term instanceof WP_Term ) ) {
			return [];
		}

		return [
			'name'             => $term->name,
			'description'      => $term->description,
			'auto_description' => Helper::truncate( $term->description ),
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
			'display_name'     => $user->display_name,
			'description'      => $user->description,
			'auto_description' => Helper::truncate( $user->description ),
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

	private function get_custom_field_data( $post ): array {
		if ( ! ( $post instanceof WP_Post ) ) {
			return [];
		}

		$meta_values = get_post_meta( $post->ID ) ?: [];
		$data        = [];
		foreach ( $meta_values as $key => $value ) {
			// Plugins like JetEngine can hook to "get_{$object_type}_metadata" to add its data from custom table
			// which might not follow WordPress standards of auto serialization/unserialization for arrays
			// so we will add a check to bypass invalid values here.
			$data[ $key ] = is_array( $value ) ? reset( $value ) : '';
		}
		return $data;
	}

	private function get_other_data(): array {
		global $page, $paged;

		return [
			'current' => [
				'year'  => wp_date( 'Y' ),
				'month' => wp_date( 'm' ),
			],
			// Translators: %s - page number
			'page'    => $paged >= 2 || $page >= 2 ? sprintf( __( 'Page %s', 'slim-seo' ), max( $paged, $page ) ) : '',
			'sep'     => '{{ sep }}', // Do not replace it yet. See Helper::normalize().
		];
	}

	private function normalize( $key ) {
		return str_replace( '-', '_', $key );
	}

	/**
	 * Get post content with filters for page builders to modify the content.
	 * Also has a filter to skip the content for certain pages, like WooCommerce checkout, cart, account, etc.
	 *
	 * @param int $post_id Post ID.
	 * @param string $content Optional. Custom post content, used for live preview when the new content is not saved yet.
	 * @return string
	 */
	public static function get_post_content( int $post_id = 0, string $content = '' ): string {
		if ( apply_filters( 'slim_seo_no_post_content', false, $post_id ) ) {
			return '';
		}

		$post = get_post( $post_id );
		if ( empty( $post ) ) {
			return '';
		}

		$content = $content ?: $post->post_content;

		return (string) apply_filters( 'slim_seo_post_content', $content, $post );
	}
}
