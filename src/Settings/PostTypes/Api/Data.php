<?php
namespace SlimSEO\Settings\PostTypes\Api;
use WP_REST_Server;
use WP_REST_Request;

class Data extends Base {
	public function register_routes() {
		register_rest_route( 'slim-seo-post-types', 'variables', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_variables' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
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

	private function normalize( $key ) {
		return str_replace( '-', '_', $key );
	}
}
