<?php
namespace SlimSEO\Settings\Content;

use WP_REST_Server;
use WP_REST_Request;
use SlimSEO\Helpers\Data;

class RestApi {
	public function setup() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		register_rest_route( 'slim-seo', '/content/option', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_option' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/single', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_single_option' ],
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

		register_rest_route( 'slim-seo', '/content/render', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'render' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', '/content/render_post_title', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'render_post_title' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function has_permission() {
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
			'wp_pattern_category'
		], '' );
		return array_diff_key( get_option( 'slim_seo' ), $exclude );
	}

	public function get_single_option( WP_REST_Request $request ): array {
		$option = $this->get_option();
		if ( ! $option[ $request->get_param( 'name' ) ] ) {
			return [];
		}

		$id     = $request->get_param( 'ID' );
		$single = $option[ $request->get_param( 'name' ) ];
		array_walk( $single, function( &$meta ) use ( $id ) {
			$meta = Data::render( $meta, $id );
		} );

		return $single;
	}

	public function get_variables() {
		$taxonomies = Data::get_taxonomies();
		$options    = [];
		foreach ( $taxonomies as $taxonomy ) {
			$key                          = $this->normalize( $taxonomy->name );
			$options[ "post.tax.{$key}" ] = $taxonomy->label;
		}

		$variables = [
			[
				'label'   => __( 'Post', 'slim-seo' ),
				'options' => [
					'post.title'         => __( 'Post title', 'slim-seo' ),
					'post.ID'            => __( 'Post ID', 'slim-seo' ),
					'post.excerpt'       => __( 'Post excerpt', 'slim-seo' ),
					'post.content'       => __( 'Post content', 'slim-seo' ),
					'post.url'           => __( 'Post URL', 'slim-seo' ),
					'post.slug'          => __( 'Post slug', 'slim-seo' ),
					'post.date'          => __( 'Post date', 'slim-seo' ),
					'post.modified_date' => __( 'Post modified date', 'slim-seo' ),
					'post.thumbnail'     => __( 'Post thumbnail', 'slim-seo' ),
					'post.comment_count' => __( 'Post comment count', 'slim-seo' ),
					'post.custom_field'  => __( 'Post custom field', 'slim-seo' ),
					'post.word_count'    => __( 'Post word count', 'slim-seo' ),
					'post.tags'          => __( 'Post tags', 'slim-seo' ),
					'post.categories'    => __( 'Post categories', 'slim-seo' ),
				],
			],
			[
				'label'   => __( 'Post taxonomy terms', 'slim-seo' ),
				'options' => $options,
			],
			[
				'label'   => __( 'Term', 'slim-seo' ),
				'options' => [
					'term.ID'          => __( 'Term ID', 'slim-seo' ),
					'term.name'        => __( 'Term name', 'slim-seo' ),
					'term.slug'        => __( 'Term slug', 'slim-seo' ),
					'term.taxonomy'    => __( 'Term taxonomy', 'slim-seo' ),
					'term.description' => __( 'Term description', 'slim-seo' ),
					'term.url'         => __( 'Term URL', 'slim-seo' ),
				],
			],
			[
				'label'   => __( 'Author', 'slim-seo' ),
				'options' => [
					'author.ID'           => __( 'Author ID', 'slim-seo' ),
					'author.first_name'   => __( 'Author first name', 'slim-seo' ),
					'author.last_name'    => __( 'Author last name', 'slim-seo' ),
					'author.display_name' => __( 'Author display name', 'slim-seo' ),
					'author.login'        => __( 'Author username', 'slim-seo' ),
					'author.nickname'     => __( 'Author nickname', 'slim-seo' ),
					'author.email'        => __( 'Author email', 'slim-seo' ),
					'author.url'          => __( 'Author website URL', 'slim-seo' ),
					'author.nicename'     => __( 'Author nicename', 'slim-seo' ),
					'author.description'  => __( 'Author description', 'slim-seo' ),
					'author.posts_url'    => __( 'Author posts URL', 'slim-seo' ),
					'author.avatar'       => __( 'Author avatar', 'slim-seo' ),
				],
			],
			[
				'label'   => __( 'Current user', 'slim-seo' ),
				'options' => [
					'user.ID'           => __( 'User ID', 'slim-seo' ),
					'user.first_name'   => __( 'User first name', 'slim-seo' ),
					'user.last_name'    => __( 'User last name', 'slim-seo' ),
					'user.display_name' => __( 'User display name', 'slim-seo' ),
					'user.login'        => __( 'User username', 'slim-seo' ),
					'user.nickname'     => __( 'User nickname', 'slim-seo' ),
					'user.email'        => __( 'User email', 'slim-seo' ),
					'user.url'          => __( 'User website URL', 'slim-seo' ),
					'user.nicename'     => __( 'User nicename', 'slim-seo' ),
					'user.description'  => __( 'User description', 'slim-seo' ),
					'user.posts_url'    => __( 'User posts URL', 'slim-seo' ),
					'user.avatar'       => __( 'User avatar', 'slim-seo' ),
				],
			],
			[
				'label'   => __( 'Site', 'slim-seo' ),
				'options' => [
					'site.title'       => __( 'Site title', 'slim-seo' ),
					'site.description' => __( 'Site description', 'slim-seo' ),
					'site.url'         => __( 'Site URL', 'slim-seo' ),
					'site.language'    => __( 'Site language', 'slim-seo' ),
					'site.icon'        => __( 'Site icon', 'slim-seo' ),
				],
			],
			[
				'label'   => __( 'Others', 'slim-seo' ),
				'options' => [
					'current.year'       => __( 'Current year', 'slim-seo' ),
					'current.month'      => __( 'Current month', 'slim-seo' ),
					'current.day'        => __( 'Current day of the month', 'slim-seo' ),
					'current.date'       => __( 'Current date', 'slim-seo' ),
					'current.time'       => __( 'Current time', 'slim-seo' ),
					'pagination.current' => __( 'Current page (pagination)', 'slim-seo' ),
					'pagination.total'   => __( 'Total number of pages (pagination)', 'slim-seo' ),
					'separator'          => __( 'Separator character', 'slim-seo' ),
				],
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
			[
				'label'   => __( 'Author', 'slim-seo' ),
				'options' => [
					'author.avatar' => __( 'Author avatar', 'slim-seo' ),
				],
			],
			[
				'label'   => __( 'Current user', 'slim-seo' ),
				'options' => [
					'user.avatar' => __( 'User avatar', 'slim-seo' ),
				],
			],
			[
				'label'   => __( 'Site', 'slim-seo' ),
				'options' => [
					'site.icon' => __( 'Site icon', 'slim-seo' ),
				],
			],
		];
		return apply_filters( 'slim_seo_image_variables', $variables );
	}

	public function get_meta_keys() {
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

	private function exclude_defaults( $meta_keys ) {
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

	private function normalize( string $key ): string {
		return str_replace( '-', '_', $key );
	}

	public function render( WP_REST_Request $request ): string {
		if ( ! $request->get_param( 'ID' ) ) {
			return '';
		}

		return Data::render( $request->get_param( 'text' ), $request->get_param( 'ID' ) );
	}

	public function render_post_title( WP_REST_Request $request ): array {
		$post_id = (int) $request->get_param( 'ID' );
		$title   = (string) $request->get_param( 'title' ); // Live post title
		if ( ! $post_id ) {
			return [
				'preview' => '',
				'default' => '',
			];
		}

		$data = [];
		if ( $title ) {
			$data['post'] = [ 'title' => $title ];
		}

		return [
			'preview' => Data::render( $request->get_param( 'text' ), $post_id, $data ),
			'default' => $this->render_default_post_title( $post_id, $title, $data ),
		];
	}

	private function render_default_post_title( int $post_id, string $title, array $data = [] ): string {
		$option    = get_option( 'slim_seo', [] );
		$post_type = get_post_type( $post_id );
		$settings  = $option[ $post_type ]['title'] ?? '{{ post.title }} {{ separator }} {{ site.title }}';

		return Data::render( $settings, $post_id, $data );
	}
}