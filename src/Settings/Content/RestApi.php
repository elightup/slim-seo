<?php
namespace SlimSEO\Settings\Content;

use WP_REST_Server;
use WP_REST_Request;
use WP_Term;
use SlimSEO\Helpers\Arr;
use SlimSEO\MetaTags\Helper;
use SlimSEO\Helpers\Data;

class RestApi {
	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes(): void {
		register_rest_route( 'slim-seo', '/content/option', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_option' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/variables', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_variables' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/image_variables', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_image_variables' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/meta_keys', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_meta_keys' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/render_post_title', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'render_post_title' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/render_term_title', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'render_term_title' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/render_post_description', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'render_post_description' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/render_term_description', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'render_term_description' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function has_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	public function get_option(): array {
		$exclude = array_fill_keys( [
			'auto_redirection',
			'enable_404_logs',
			'features',
			'footer_code',
			'force_trailing_slash',
			'header_code',
			'notification_dismissed',
			'default_facebook_image',
			'default_twitter_image',
			'facebook_app_id',
			'twitter_site',
			'default_linkedin_image',
			'wp_pattern_category',
		], '' );
		return array_diff_key( get_option( 'slim_seo' ), $exclude );
	}

	public function get_variables() {
		$taxonomies = Data::get_taxonomies();
		unset( $taxonomies['category'], $taxonomies['post_tag'] );

		$taxonomy_options = [];
		foreach ( $taxonomies as $taxonomy ) {
			$key                          = $this->normalize( $taxonomy->name );
			$taxonomy_options[ "post.tax.{$key}" ] = $taxonomy->label;
		}

		$variables   = [];
		$variables[] = [
			'label'   => __( 'Post', 'slim-seo' ),
			'options' => [
				'post.title'         => __( 'Post title', 'slim-seo' ),
				'post.excerpt'       => __( 'Post excerpt', 'slim-seo' ),
				'post.content'       => __( 'Post content', 'slim-seo' ),
				'post.date'          => __( 'Post date', 'slim-seo' ),
				'post.modified_date' => __( 'Post modified date', 'slim-seo' ),
				'post.thumbnail'     => __( 'Post thumbnail', 'slim-seo' ),
				'post.custom_field'  => __( 'Post custom field', 'slim-seo' ),
				'post.tags'          => __( 'Post tags', 'slim-seo' ),
				'post.categories'    => __( 'Post categories', 'slim-seo' ),
			],
		];
		if ( $taxonomy_options ) {
			$variables[] = [
				'label'   => __( 'Post taxonomy terms', 'slim-seo' ),
				'options' => $taxonomy_options,
			];
		}
		$variables[]     = [
			'label'   => __( 'Term', 'slim-seo' ),
			'options' => [
				'term.name'        => __( 'Term name', 'slim-seo' ),
				'term.description' => __( 'Term description', 'slim-seo' ),
			],
		];
		$variables[] = [
			'label'   => __( 'Author', 'slim-seo' ),
			'options' => [
				'author.display_name' => __( 'Author display name', 'slim-seo' ),
				'author.description'  => __( 'Author description', 'slim-seo' ),
			],
		];
		$variables[] = [

			'label'   => __( 'Current user', 'slim-seo' ),
			'options' => [
				'user.display_name' => __( 'User display name', 'slim-seo' ),
				'user.description'  => __( 'User description', 'slim-seo' ),
			],
		];
		$variables[] = [
			'label'   => __( 'Site', 'slim-seo' ),
			'options' => [
				'site.title'       => __( 'Site title', 'slim-seo' ),
				'site.description' => __( 'Site description', 'slim-seo' ),
			],
		];
		$variables[] = [
			'label'   => __( 'Others', 'slim-seo' ),
			'options' => [
				'current.year' => __( 'Current year', 'slim-seo' ),
				'page'         => __( 'Current page number', 'slim-seo' ),
				'sep'          => __( 'Separator character', 'slim-seo' ),
			],
		];

		return apply_filters( 'slim_seo_variables', $variables );
	}

	public function get_image_variables() {
		$variables = [
			[
				'label'   => __( 'Post', 'slim-seo' ),
				'options' => [
					'post.thumbnail' => __( 'Post thumbnail', 'slim-seo' ),
				],
			],
		];
		return apply_filters( 'slim_seo_image_variables', $variables );
	}

	public function get_meta_keys(): array {
		global $wpdb;
		$meta_keys = $wpdb->get_col( "SELECT DISTINCT meta_key FROM $wpdb->postmeta ORDER BY meta_key" );
		$meta_keys = $this->exclude_defaults( $meta_keys );
		$options   = [];
		foreach ( $meta_keys as $meta_key ) {
			$options[] = [
				'value' => $meta_key,
				'label' => $meta_key,
			];
		}

		return $options;
	}

	private function exclude_defaults( array $meta_keys ): array {
		$default = [
			'_edit_last',
			'_edit_lock',
			'_encloseme',
			'_event_name',
			'_generate-full-width-content',
			'_icon',
			'_menu_item_classes',
			'_menu_item_menu_item_parent',
			'_menu_item_object',
			'_menu_item_object_id',
			'_menu_item_target',
			'_menu_item_type',
			'_menu_item_url',
			'_menu_item_xfn',
			'_pingme',
			'_primary_term_category',
			'_thumbnail_id',
			'_wp_attached_file',
			'_wp_attachment_image_alt',
			'_wp_attachment_metadata',
			'_wp_desired_post_slug',
			'_wp_old_date',
			'_wp_page_template',
			'_wp_suggested_privacy_policy_content',
			'_wp_trash_meta_status',
			'_wp_trash_meta_time',
		];
		return array_values( array_diff( $meta_keys, $default ) );
	}

	private function normalize( string $key ): string {
		return str_replace( '-', '_', $key );
	}

	public function render_post_title( WP_REST_Request $request ): array {
		return $this->render_title( $request, 'post' );
	}

	public function render_term_title( WP_REST_Request $request ): array {
		return $this->render_title( $request, 'term' );
	}

	private function render_title( WP_REST_Request $request, string $object_type = 'post' ): array {
		$id = (int) $request->get_param( 'ID' );
		if ( ! $id ) {
			return [
				'preview' => '',
				'default' => '',
			];
		}

		$text  = (string) $request->get_param( 'text' ); // Manual entered meta title
		$title = (string) $request->get_param( 'title' ); // Live title

		$data = [];
		if ( $title ) {
			$data[ $object_type ] = [ 'title' => $title ];
		}

		$default = $object_type === 'post' ? $this->get_default_post_title( $id ) : $this->get_default_term_title( $id );
		$preview = Helper::render( $text, $id, $data );
		if ( ! $preview ) {
			$preview = Helper::render( $default, $id, $data );
		}

		return compact( 'preview', 'default' );
	}

	private function get_default_post_title( int $post_id ): string {
		$is_home = 'page' === get_option( 'show_on_front' ) && $post_id == get_option( 'page_on_front' );
		if ( $is_home ) {
			$default = '{{ site.title }} {{ sep }} {{ site.description }}';
			$key     = 'home';
		} else {
			$default = '{{ post.title }} {{ page }} {{ sep }} {{ site.title }}';
			$key     = get_post_type( $post_id );
		}

		if ( ! $key ) {
			return $default;
		}
		$option = get_option( 'slim_seo', [] );
		return Arr::get( $option, "$key.title", $default );
	}

	private function get_default_term_title( int $term_id ): string {
		$default = '{{ term.title }} {{ page }} {{ sep }} {{ site.title }}';
		$term    = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return $default;
		}
		$option = get_option( 'slim_seo', [] );
		return Arr::get( $option, "{$term->taxonomy}.title", $default );
	}

	public function render_post_description( WP_REST_Request $request ): array {
		return $this->render_description( $request, 'post' );
	}

	public function render_term_description( WP_REST_Request $request ): array {
		return $this->render_description( $request, 'term' );
	}

	private function render_description( WP_REST_Request $request, string $object_type = 'post' ): array {
		$id = (int) $request->get_param( 'ID' );
		if ( ! $id ) {
			return [
				'preview' => '',
				'default' => '',
			];
		}

		$text        = (string) $request->get_param( 'text' ); // Manual entered meta description
		$description = (string) $request->get_param( 'description' ); // Live description
		$data        = [];

		if ( $description && ( 'term' === $object_type ) ) {
			$data[ $object_type ] = [ 'description' => $description ];
		}
		if ( $description && ( 'post' === $object_type ) ) {
			$data[ $object_type ] = [
				'excerpt' => $description,
				'content' => $description,
			];
		}

		$default = ( $object_type === 'post' ? $this->get_default_post_description( $id ) : $this->get_default_term_description( $id ) ) ?: $description;
		$preview = Helper::render( $text, $id, $data );
		if ( ! $preview ) {
			$preview = Helper::render( $default, $id, $data ) ?: $description;
		}

		return compact( 'preview', 'default' );
	}

	private function get_default_post_description( int $post_id ): string {
		$is_home = 'page' === get_option( 'show_on_front' ) && $post_id == get_option( 'page_on_front' );
		$key     = $is_home ? 'home' : get_post_type( $post_id );

		if ( ! $key ) {
			return $default;
		}
		$option = get_option( 'slim_seo', [] );
		return $option[ $key ]['description'] ?? '';
	}

	private function get_default_term_description( int $term_id ): string {
		$term    = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return $default;
		}
		$option = get_option( 'slim_seo', [] );
		return $option[ $term->taxonomy ]['description'] ?? '';
	}
}
