<?php
namespace SlimSEO\MetaTags;

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
			[ 'post' => new Data\Post( $this->post_id ) ],
			[ 'term' => new Data\Term( $this->term_id ) ],
			[ 'post_type' => $this->get_post_type_data() ],
			[ 'author' => $this->get_author_data() ],
			[ 'user' => $this->get_user_data() ],
			[ 'site' => $this->get_site_data() ],
			$this->get_other_data()
		);
		$this->data = apply_filters( 'slim_seo_data', $this->data, $this->post_id, $this->term_id );

		return $this->data;
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

	private function get_user_data() {
		return $this->get_user( get_current_user_id() );
	}

	private function get_author_data(): array {
		$post = get_post( $this->post_id ?: QueriedObject::get_id() );
		return empty( $post ) ? [] : $this->get_user( $post->post_author );
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
