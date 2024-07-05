<?php
namespace SlimSEO\Settings;

use WP_REST_Server;

class PostTypes {
	public function setup() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		register_rest_route( 'slim-seo', 'post-types-option', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_option' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'variables', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_variables' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'meta_keys', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_meta_keys' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function has_permission() {
		return current_user_can( 'manage_options' );
	}

	public function get_option(): array {
		$exclude = array_fill_keys( [
			'home',
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
		], '' );
		return array_diff_key( get_option( 'slim_seo' ), $exclude );
	}

	public function get_variables() {
		$taxonomies = $this->get_taxonomies();
		$options    = [];
		foreach ( $taxonomies as $taxonomy ) {
			$key = $this->normalize( $taxonomy['slug'] );
			$options[ "post.tax.{$key}" ] = $taxonomy['name'];
		}

		$variables = [
			[
				'label'   => __( 'Post', 'slim-seo-schema' ),
				'options' => [
					'post.title'         => __( 'Post title', 'slim-seo-schema' ),
					'post.ID'            => __( 'Post ID', 'slim-seo-schema' ),
					'post.excerpt'       => __( 'Post excerpt', 'slim-seo-schema' ),
					'post.content'       => __( 'Post content', 'slim-seo-schema' ),
					'post.url'           => __( 'Post URL', 'slim-seo-schema' ),
					'post.slug'          => __( 'Post slug', 'slim-seo-schema' ),
					'post.date'          => __( 'Post date', 'slim-seo-schema' ),
					'post.modified_date' => __( 'Post modified date', 'slim-seo-schema' ),
					'post.thumbnail'     => __( 'Post thumbnail', 'slim-seo-schema' ),
					'post.comment_count' => __( 'Post comment count', 'slim-seo-schema' ),
					'post.custom_field'  => __( 'Post custom field', 'slim-seo-schema' ),
					'post.word_count'    => __( 'Post word count', 'slim-seo-schema' ),
					'post.tags'          => __( 'Post tags', 'slim-seo-schema' ),
					'post.categories'    => __( 'Post categories', 'slim-seo-schema' ),
				],
			],
			[
				'label'   => __( 'Post taxonomy terms', 'slim-seo-schema' ),
				'options' => $options,
			],
			[
				'label'   => __( 'Author', 'slim-seo-schema' ),
				'options' => [
					'author.ID'           => __( 'Author ID', 'slim-seo-schema' ),
					'author.first_name'   => __( 'Author first name', 'slim-seo-schema' ),
					'author.last_name'    => __( 'Author last name', 'slim-seo-schema' ),
					'author.display_name' => __( 'Author display name', 'slim-seo-schema' ),
					'author.username'     => __( 'Author username', 'slim-seo-schema' ),
					'author.nickname'     => __( 'Author nickname', 'slim-seo-schema' ),
					'author.email'        => __( 'Author email', 'slim-seo-schema' ),
					'author.website_url'  => __( 'Author website URL', 'slim-seo-schema' ),
					'author.nicename'     => __( 'Author nicename', 'slim-seo-schema' ),
					'author.description'  => __( 'Author description', 'slim-seo-schema' ),
					'author.posts_url'    => __( 'Author posts URL', 'slim-seo-schema' ),
					'author.avatar'       => __( 'Author avatar', 'slim-seo-schema' ),
				],
			],
			[
				'label'   => __( 'Current user', 'slim-seo-schema' ),
				'options' => [
					'user.ID'           => __( 'User ID', 'slim-seo-schema' ),
					'user.first_name'   => __( 'User first name', 'slim-seo-schema' ),
					'user.last_name'    => __( 'User last name', 'slim-seo-schema' ),
					'user.display_name' => __( 'User display name', 'slim-seo-schema' ),
					'user.username'     => __( 'User username', 'slim-seo-schema' ),
					'user.nickname'     => __( 'User nickname', 'slim-seo-schema' ),
					'user.email'        => __( 'User email', 'slim-seo-schema' ),
					'user.website_url'  => __( 'User website URL', 'slim-seo-schema' ),
					'user.nicename'     => __( 'User nicename', 'slim-seo-schema' ),
					'user.description'  => __( 'User description', 'slim-seo-schema' ),
					'user.posts_url'    => __( 'User posts URL', 'slim-seo-schema' ),
					'user.avatar'       => __( 'User avatar', 'slim-seo-schema' ),
				],
			],
			[
				'label'   => __( 'Site', 'slim-seo-schema' ),
				'options' => [
					'site.title'       => __( 'Site title', 'slim-seo-schema' ),
					'site.description' => __( 'Site description', 'slim-seo-schema' ),
					'site.url'         => __( 'Site URL', 'slim-seo-schema' ),
					'site.language'    => __( 'Site language', 'slim-seo-schema' ),
					'site.icon'        => __( 'Site icon', 'slim-seo-schema' ),
				],
			],
			[
				'label'   => __( 'Current page', 'slim-seo-schema' ),
				'options' => [
					'current.title' => __( 'Current page title', 'slim-seo-schema' ),
					'current.url'   => __( 'Current page URL', 'slim-seo-schema' ),
				],
			],
		];
		return apply_filters( 'slim_seo_variables', $variables );
	}

	private function get_taxonomies() {
		$unsupported = [
			'wp_theme',
			'wp_template_part_area',
			'link_category',
			'nav_menu',
			'post_format',
			'mb-views-category',
		];
		$taxonomies  = get_taxonomies( [], 'objects' );
		$taxonomies  = array_diff_key( $taxonomies, array_flip( $unsupported ) );
		$taxonomies  = array_map( function( $taxonomy ) {
			return [
				'slug' => $taxonomy->name,
				'name' => $taxonomy->label,
			];
		}, $taxonomies );

		return array_values( $taxonomies );
	}

	public function get_meta_keys() {
		global $wpdb;
		$meta_keys = $wpdb->get_col( "SELECT DISTINCT meta_key FROM $wpdb->postmeta ORDER BY meta_key" );
		$meta_keys = $this->exclude_defaults( $meta_keys );
		$options   = [];
		foreach ( $meta_keys as $key => $value ) {
			$options[] = [
				'value' => $value,
				'label' => $value,
			];
		}

		return $options;
	}

	public function exclude_defaults( $meta_keys ) {
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
			'action_priority',
			'action_type',
			'data',
			'event_name',
			'fields',
			'icon',
			'mb_testimonials_columns',
			'mb_testimonials_display_arrows',
			'mb_testimonials_display_dots',
			'mb_testimonials_dots',
			'mb_testimonials_enable_slider',
			'mb_testimonials_group',
			'mb_testimonials_image_width',
			'mb_testimonials_number_slider_items',
			'mb_testimonials_number_slider_items_desktop',
			'mb_testimonials_number_slider_items_mobile',
			'mb_testimonials_style',
			'mb_testimonials_transition',
			'mb_views_custom_css',
			'mbfp_count',
			'mbfp_posts',
			'menu-icons',
			'meta_box',
			'mobile',
			'mode',
			'position',
			'relationship',
			'settings',
			'singular_locations',
			'slim_seo',
			'type',
		];
		return array_values( array_diff( $meta_keys, $default ) );
	}

	private function normalize( $key ) {
		return str_replace( '-', '_', $key );
	}
}
