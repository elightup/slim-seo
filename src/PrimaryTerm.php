<?php
namespace SlimSEO;

use SlimSEO\Helpers\Data;
use eLightUp\SlimSEO\Common\Helpers\Data as CommonHelpersData;

class PrimaryTerm {
	const META_PREFIX = '_slim_seo_primary_term_';

	public function setup(): void {
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

		$post_id         = get_the_ID();
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

		$js_file_type = $screen->is_block_editor() ? 'block' : 'classic';
		$dependencies = [];

		if ( $screen->is_block_editor() ) {
			$dependencies = [ 'wp-data', 'wp-element', 'wp-hooks', 'wp-compose' ];
		} else {
			$dependencies      = [ 'jquery' ];
			$params['setText'] = __( 'Set primary', 'slim-seo' );
			$params['nonce']   = wp_create_nonce( 'save' );
		}

		wp_enqueue_script( 'slim-seo-primary-term', SLIM_SEO_URL . "js/build/primary-term-{$js_file_type}.js", $dependencies, filemtime( SLIM_SEO_DIR . "js/build/primary-term-{$js_file_type}.js" ), true );
		wp_localize_script( 'slim-seo-primary-term', 'ssPrimaryTerm', $params );
	}

	public function register_meta() {
		$taxonomies = $this->get_taxonomies();

		foreach ( $taxonomies as $taxonomy ) {
			$post_types = get_taxonomy( $taxonomy )->object_type ?? [];

			foreach ( $post_types as $post_type ) {
				register_post_meta( $post_type, self::META_PREFIX . $taxonomy, [
					'single'        => true,
					'type'          => 'integer',
					'default'       => 0,
					'show_in_rest'  => true,
					'auth_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
				] );
			}
		}
	}

	public function save( int $post_id ): void {
		if ( ! check_ajax_referer( 'save', 'ss_primary_term_nonce', false ) || empty( $_POST ) ) {
			return;
		}

		$taxonomies = $this->get_taxonomies( isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : '' );

		foreach ( $taxonomies as $taxonomy ) {
			$meta_key = self::META_PREFIX . $taxonomy;

			if ( ! empty( $_POST[ $meta_key ] ) ) {
				update_post_meta( $post_id, $meta_key, (int) $_POST[ $meta_key ] );
			} else {
				delete_post_meta( $post_id, $meta_key );
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

	private function get_supported_rewrite_post_types(): array {
		$supported_rewrite_post_types = [
			'post' => [
				'taxonomy'  => 'category',
				'structure' => get_option( 'permalink_structure', '' ),
			],
		];

		if ( defined( 'WC_PLUGIN_FILE' ) ) {
			$supported_rewrite_post_types['product'] = [
				'taxonomy'  => 'product_cat',
				'structure' => get_option( 'woocommerce_permalinks', [] )['product_base'] ?? '',
			];
		}

		$supported_rewrite_post_types = apply_filters( 'slim_seo_primary_term_supported_rewrite_post_types', $supported_rewrite_post_types );

		return $supported_rewrite_post_types;
	}

	public function filter_permalink( string $permalink, $post ): string {
		$post = get_post( $post );

		if ( ! $post ) {
			return $permalink;
		}

		$supported_rewrite_post_types = $this->get_supported_rewrite_post_types();

		if ( ! isset( $supported_rewrite_post_types[ $post->post_type ] ) ) {
			return $permalink;
		}

		$rewrite_data = $supported_rewrite_post_types[ $post->post_type ];
		$taxonomy     = $rewrite_data['taxonomy'];
		$structure    = $rewrite_data['structure'];
		$placeholder  = '%' . $taxonomy . '%';

		if ( false === strpos( $structure, $placeholder ) ) {
			return $permalink;
		}

		$primary_id = self::get_primary_term_id( $post->ID, $taxonomy );

		if ( ! $primary_id ) {
			return $permalink;
		}

		$primary_term = get_term( $primary_id, $taxonomy );

		if ( ! $primary_term || is_wp_error( $primary_term ) ) {
			return $permalink;
		}

		$term_path = $this->get_term_path( $primary_term, $taxonomy );

		return $this->replace_term_in_permalink( $permalink, $taxonomy, $term_path, $post );
	}

	private function get_term_path( \WP_Term $term, string $taxonomy ): string {
		$taxonomy_object = get_taxonomy( $taxonomy );

		if ( ! $taxonomy_object->rewrite['hierarchical'] ?? false ) {
			return $term->slug;
		}

		$ancestors = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
		$ancestors = array_reverse( $ancestors );
		$slugs     = [];

		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $taxonomy );

			if ( $ancestor && ! is_wp_error( $ancestor ) ) {
				$slugs[] = $ancestor->slug;
			}
		}

		$slugs[] = $term->slug;

		return implode( '/', $slugs );
	}

	private function replace_term_in_permalink( string $permalink, string $taxonomy, string $term_path, \WP_Post $post ): string {
		$terms = get_the_terms( $post->ID, $taxonomy );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return $permalink;
		}

		usort( $terms, fn( $a, $b ) => $a->term_id - $b->term_id );

		$default_term = $terms[0];
		$default_path = $this->get_term_path( $default_term, $taxonomy );

		return str_replace( '/' . $default_path . '/', '/' . $term_path . '/', $permalink );
	}

	public function breadcrumbs_term( \WP_Term $term, int $post_id ): \WP_Term {
		$primary_id = self::get_primary_term_id( $post_id, $term->taxonomy );

		if ( $primary_id ) {
			$term = get_term( $primary_id, $term->taxonomy );
		}

		return $term;
	}

	public static function get_primary_term_id( int $post_id, string $taxonomy ): int {
		$primary_id = get_post_meta( $post_id, self::META_PREFIX . $taxonomy, true );

		return $primary_id ? (int) $primary_id : 0;
	}
}
