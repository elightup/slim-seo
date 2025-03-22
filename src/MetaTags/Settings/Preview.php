<?php
namespace SlimSEO\MetaTags\Settings;

use WP_REST_Server;
use WP_REST_Request;
use WP_Term;
use SlimSEO\Helpers\Option;
use SlimSEO\MetaTags\Helper;
use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Data;

class Preview {
	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes(): void {
		register_rest_route( 'slim-seo', 'meta-tags/render_post_title', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_post_title' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'meta-tags/render_term_title', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_term_title' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'meta-tags/render_post_description', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_post_description' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'meta-tags/render_term_description', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_term_description' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo', 'meta-tags/render_text', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_text' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function has_permission(): bool {
		return current_user_can( 'edit_posts' );
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

		if ( $object_type === 'post' ) {
			$data = [
				'post' => [
					'title' => $title,
				],
			];
		} else {
			$data = [
				'term' => [
					'name' => $title,
				],
			];
		}

		$default = $object_type === 'post' ? $this->get_default_post_title( $id ) : $this->get_default_term_title( $id );
		if ( $object_type === 'post' ) {
			$preview = Helper::render( $text ?: $default, $id, 0, $data );
		} else {
			$preview = Helper::render( $text ?: $default, 0, $id, $data );
		}

		return compact( 'preview', 'default' );
	}

	private function get_default_post_title( int $post_id ): string {
		// For static frontpage: don't use page's settings, use WordPress default instead.
		$is_static_frontpage = 'page' === get_option( 'show_on_front' ) && $post_id === (int) get_option( 'page_on_front' );
		if ( $is_static_frontpage ) {
			return Title::DEFAULTS['home'];
		}

		$default = Title::DEFAULTS['post'];
		$key     = get_post_type( $post_id );

		return $key ? Option::get( "$key.title", $default ) : $default;
	}

	private function get_default_term_title( int $term_id ): string {
		$default = Title::DEFAULTS['term'];
		$term    = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return $default;
		}
		return Option::get( "{$term->taxonomy}.title", $default );
	}

	public function render_term_description( WP_REST_Request $request ): array {
		$id = (int) $request->get_param( 'ID' );
		if ( ! $id ) {
			return [
				'preview' => '',
				'default' => '',
			];
		}

		$text        = (string) $request->get_param( 'text' ); // Manual entered meta description
		$description = (string) $request->get_param( 'description' ); // Live description
		$data        = [
			'term' => [
				'description'      => $description,
				'auto_description' => Helper::truncate( $description ),
			],
		];

		$default = $this->get_default_term_description( $id );
		$preview = Helper::render( $text ?: $default, 0, $id, $data );

		return compact( 'preview', 'default' );
	}

	private function get_default_term_description( int $term_id ): string {
		$default = Description::DEFAULTS['term'];
		$term    = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return $default;
		}
		return Option::get( "{$term->taxonomy}.description", $default );
	}

	public function render_post_description( WP_REST_Request $request ): array {
		$id = (int) $request->get_param( 'ID' );
		if ( ! $id ) {
			return [
				'preview' => '',
				'default' => '',
			];
		}

		$text    = (string) $request->get_param( 'text' ); // Manual entered meta description
		$excerpt = (string) $request->get_param( 'excerpt' ); // Live excerpt
		$content = (string) $request->get_param( 'content' ); // Live content
		$content = Data::get_post_content( $id, $content );

		$data         = [];
		$data['post'] = array_filter( [
			'excerpt'          => $excerpt,
			'content'          => $content,
			'auto_description' => Helper::truncate( $excerpt ?: $content ),
		] );
		$data         = array_filter( $data );

		$default = $this->get_default_post_description( $id );
		$preview = Helper::render( $text ?: $default, $id, 0, $data );

		return compact( 'preview', 'default' );
	}

	private function get_default_post_description( int $post_id ): string {
		$default = Description::DEFAULTS['post'];
		$key     = get_post_type( $post_id );

		return $key ? Option::get( "$key.description", $default ) : $default;
	}

	public function render_text( WP_REST_Request $request ): string {
		$id   = (int) $request->get_param( 'ID' );
		$text = (string) $request->get_param( 'text' );
		return Helper::render( $text, $id );
	}
}
