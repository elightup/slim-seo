<?php
namespace SlimSEO;

use SlimSEO\Helpers\Assets;
use SlimSEO\Helpers\Data;
use eLightUp\SlimSEO\Common\Helpers\Data as CommonHelpersData;
use WP_Term;
use WP_Post;

class PrimaryTerm {
	const META_PREFIX = '_slim_seo_primary_term_';

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'init', [ $this, 'register_meta' ] );
		add_action( 'save_post', [ $this, 'save' ] );
		add_filter( 'post_link', [ $this, 'filter_permalink' ], 9999, 2 );
		add_filter( 'post_type_link', [ $this, 'filter_permalink' ], 9999, 2 );
		add_filter( 'slim_seo_breadcrumbs_term', [ $this, 'breadcrumbs_term' ], 10, 2 );
	}

	public function enqueue( string $hook ): void {
		if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->post_type, Data::get_meta_box_post_types(), true ) ) {
			return;
		}

		$post_id         = (int) ( $_GET['post'] ?? 0 ); // phpcs:ignore
		$taxonomies      = $this->get_taxonomies( $screen->post_type );
		$taxonomies_data = [];

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_object = get_taxonomy( $taxonomy );

			$taxonomies_data[ $taxonomy ] = [
				'label'        => $taxonomy_object->labels->singular_name ?? $taxonomy_object->label,
				'primaryValue' => self::get_primary_term_id( $post_id, $taxonomy ),
				'metaKey'      => self::META_PREFIX . $taxonomy,
			];
		}

		$params = [
			'taxonomies'  => $taxonomies_data,
			'primaryText' => __( 'Primary', 'slim-seo' ),
		];

		wp_enqueue_style( 'slim-seo-primary-term', SLIM_SEO_URL . 'css/primary-term.css', [], filemtime( SLIM_SEO_DIR . 'css/primary-term.css' ) );

		if ( $screen->is_block_editor() ) {
			Assets::enqueue_build_js( 'primary-term-block', 'ssPrimaryTerm', $params );
		} else {
			$params['setText'] = __( 'Set primary', 'slim-seo' );
			$params['nonce']   = wp_create_nonce( 'save' );

			wp_enqueue_script( 'slim-seo-primary-term', SLIM_SEO_URL . 'js/primary-term/classic-editor.js', [ 'jquery' ], filemtime( SLIM_SEO_DIR . 'js/primary-term/classic-editor.js' ), true );
			wp_localize_script( 'slim-seo-primary-term', 'ssPrimaryTerm', $params );
		}
	}

	public function register_meta(): void {
		$taxonomies = $this->get_taxonomies();

		foreach ( $taxonomies as $taxonomy ) {
			$post_types = get_taxonomy( $taxonomy )->object_type ?? [];

			foreach ( $post_types as $post_type ) {
				register_post_meta( $post_type, self::META_PREFIX . $taxonomy, [
					'single'        => true,
					'type'          => 'integer',
					'default'       => 0,
					'show_in_rest'  => true,
					'auth_callback' => [ $this, 'can_edit_post' ],
				] );
			}
		}
	}

	public function save( int $post_id ): void {
		if (
			empty( $_POST['ss_primary_term_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ss_primary_term_nonce'] ) ), 'save' )
			|| ! $this->can_user_edit( $post_id )
		) {
			return;
		}

		$taxonomies = $this->get_taxonomies( get_post_type( $post_id ) );

		foreach ( $taxonomies as $taxonomy ) {
			$meta_key = self::META_PREFIX . $taxonomy;

			if ( empty( $_POST[ $meta_key ] ) ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, (int) $_POST[ $meta_key ] );
			}
		}
	}

	private function get_taxonomies( string $post_type = '' ): array {
		$taxonomies = CommonHelpersData::get_taxonomies();

		if ( $post_type ) {
			$taxonomies = array_filter( $taxonomies, function ( $taxonomy ) use ( $post_type ) {
				return in_array( $post_type, $taxonomy->object_type ?? [], true );
			} );
		}

		return array_keys( $taxonomies );
	}

	private function get_rewrite_data(): array {
		$data = [
			'post' => [
				'taxonomy'  => 'category',
				'structure' => get_option( 'permalink_structure', '' ),
			],
		];

		if ( defined( 'WC_PLUGIN_FILE' ) ) {
			$data['product'] = [
				'taxonomy'  => 'product_cat',
				'structure' => get_option( 'woocommerce_permalinks', [] )['product_base'] ?? '',
			];
		}

		return apply_filters( 'slim_seo_primary_term_rewrite', $data );
	}

	public function filter_permalink( string $permalink, $post ): string {
		$post = get_post( $post );
		if ( ! $post ) {
			return $permalink;
		}

		$rewrite = $this->get_rewrite_data();
		if ( ! isset( $rewrite[ $post->post_type ] ) ) {
			return $permalink;
		}

		$rewrite_data = $rewrite[ $post->post_type ];
		$taxonomy     = $rewrite_data['taxonomy'];
		$structure    = $rewrite_data['structure'];
		$placeholder  = "%$taxonomy%";

		if ( ! str_contains( $structure, $placeholder ) ) {
			return $permalink;
		}

		$primary_id = self::get_primary_term_id( $post->ID, $taxonomy );
		if ( ! $primary_id ) {
			return $permalink;
		}

		$primary_term = get_term( $primary_id, $taxonomy );
		if ( ! ( $primary_term instanceof WP_Term ) ) {
			return $permalink;
		}

		$term_path = $this->get_term_path( $primary_term, $taxonomy );

		return $this->replace_term_in_permalink( $permalink, $taxonomy, $term_path, $post );
	}

	private function get_term_path( WP_Term $term, string $taxonomy ): string {
		$taxonomy_object = get_taxonomy( $taxonomy );
		if ( empty( $taxonomy_object->rewrite['hierarchical'] ) ) {
			return $term->slug;
		}

		$ancestors = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
		$ancestors = array_reverse( $ancestors );
		$slugs     = [];

		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $taxonomy );
			if ( $ancestor instanceof WP_Term ) {
				$slugs[] = $ancestor->slug;
			}
		}

		$slugs[] = $term->slug;

		return implode( '/', $slugs );
	}

	private function replace_term_in_permalink( string $permalink, string $taxonomy, string $term_path, WP_Post $post ): string {
		$terms = get_the_terms( $post->ID, $taxonomy );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return $permalink;
		}

		foreach ( $terms as $term ) {
			$current_path = $this->get_term_path( $term, $taxonomy );
			if ( str_contains( $permalink, "/$current_path/" ) ) {
				return str_replace( "/$current_path/", "/$term_path/", $permalink );
			}
		}

		return $permalink;
	}

	public function breadcrumbs_term( WP_Term $term, int $post_id ): WP_Term {
		$primary_id = self::get_primary_term_id( $post_id, $term->taxonomy );
		if ( ! $primary_id ) {
			return $term;
		}

		$primary_term = get_term( $primary_id, $term->taxonomy );
		if ( $primary_term instanceof WP_Term ) {
			$term = $primary_term;
		}

		return $term;
	}

	public static function get_primary_term_id( int $post_id, string $taxonomy ): int {
		return (int) get_post_meta( $post_id, self::META_PREFIX . $taxonomy, true );
	}

	public function can_edit_post( bool $allowed, string $meta_key, int $post_id ): bool {
		return $this->can_user_edit( $post_id );
	}

	private function can_user_edit( int $post_id ): bool {
		return $post_id && current_user_can( 'edit_post', $post_id );
	}
}
