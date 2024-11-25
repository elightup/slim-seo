<?php
namespace SlimSEO\Settings\MetaTags;

use WP_REST_Server;
use SlimSEO\Helpers\Data;

class RestApi {
	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes(): void {
		register_rest_route( 'slim-seo', 'meta-tags/option', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'get_option' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'meta-tags/variables', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'get_variables' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'meta-tags/image_variables', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'get_image_variables' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'meta-tags/meta_keys', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'get_meta_keys' ],
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
		return array_diff_key( get_option( 'slim_seo', [] ), $exclude );
	}

	public function get_variables() {
		$taxonomies = Data::get_taxonomies();
		unset( $taxonomies['category'], $taxonomies['post_tag'] );

		$taxonomy_options = [];
		foreach ( $taxonomies as $taxonomy ) {
			$key                                   = $this->normalize( $taxonomy->name );
			$taxonomy_options[ "post.tax.{$key}" ] = $taxonomy->label;
		}

		$variables   = [];
		$variables[] = [
			'label'   => __( 'Post', 'slim-seo' ),
			'options' => [
				'post.title'            => __( 'Post title', 'slim-seo' ),
				'post.excerpt'          => __( 'Post excerpt', 'slim-seo' ),
				'post.content'          => __( 'Post content', 'slim-seo' ),
				'post.auto_description' => __( 'Post auto description', 'slim-seo' ),
				'post.date'             => __( 'Post date', 'slim-seo' ),
				'post.modified_date'    => __( 'Post modified date', 'slim-seo' ),
				'post.thumbnail'        => __( 'Post thumbnail', 'slim-seo' ),
				'post.custom_field'     => __( 'Post custom field', 'slim-seo' ),
				'post.tags'             => __( 'Post tags', 'slim-seo' ),
				'post.categories'       => __( 'Post categories', 'slim-seo' ),
			],
		];
		if ( $taxonomy_options ) {
			$variables[] = [
				'label'   => __( 'Post taxonomy terms', 'slim-seo' ),
				'options' => $taxonomy_options,
			];
		}
		$variables[] = [
			'label'   => __( 'Post type labels', 'slim-seo' ),
			'options' => [
				'post_type.singular' => __( 'Singular', 'slim-seo' ),
				'post_type.plural'   => __( 'Plural', 'slim-seo' ),
			],
		];
		$variables[] = [
			'label'   => __( 'Term', 'slim-seo' ),
			'options' => [
				'term.name'             => __( 'Term name', 'slim-seo' ),
				'term.description'      => __( 'Term description', 'slim-seo' ),
				'term.auto_description' => __( 'Term auto description', 'slim-seo' ),
			],
		];
		$variables[] = [
			'label'   => __( 'Author', 'slim-seo' ),
			'options' => [
				'author.display_name'     => __( 'Author display name', 'slim-seo' ),
				'author.description'      => __( 'Author description', 'slim-seo' ),
				'author.auto_description' => __( 'Author auto description', 'slim-seo' ),
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
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
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
}
