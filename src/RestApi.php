<?php
namespace SlimSEO;

use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Helper;
use WP_REST_Meta_Fields;
use WP_REST_Request;

class RestApi {
	private $title;
	private $description;

	public function __construct( Title $title, Description $description ) {
		$this->title       = $title;
		$this->description = $description;
	}

	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_meta' ] );
	}

	public function register_meta(): void {
		$args = [
			'type'         => 'object',
			'description'  => __( 'Search Engine Optimization', 'slim-seo' ),
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'       => 'object',
					'properties' => [
						'title'          => [ 'type' => 'string' ],
						'description'    => [ 'type' => 'string' ],
						'facebook_image' => [ 'type' => 'string' ],
						'twitter_image'  => [ 'type' => 'string' ],
						'canonical'      => [ 'type' => 'string' ],
						'noindex'        => [ 'type' => 'boolean' ],
					],
				],
			],
		];

		$args_post = $args_term = $args;
		$args_post['show_in_rest']['prepare_callback'] = [ $this, 'prepare_value_for_post' ];
		$args_term['show_in_rest']['prepare_callback'] = [ $this, 'prepare_value_for_term' ];

		register_meta( 'post', 'slim_seo', $args_post );
		register_meta( 'term', 'slim_seo', $args_term );
	}

	public function prepare_value_for_post( $value, WP_REST_Request $request, array $args ): array {
		$parsed_value = [
			'title'       => $this->get_post_meta_title(),
			'description' => $this->get_post_meta_description(),
		];
		$value = array_merge( (array) $value, $parsed_value );

		return $this->prepare_value( $value, $request, $args );
	}

	public function prepare_value_for_term( $value, WP_REST_Request $request, array $args ): array {
		$defaults = [
			'title'       => $this->title->get_term_value(),
			'description' => $this->description->get_term_value(),
		];
		$value = array_merge( $defaults, (array) $value );

		return $this->prepare_value( $value, $request, $args );
	}

	private function prepare_value( $value, WP_REST_Request $request, array $args ): array {
		// Let WordPress prepare the value first. It will auto format the value as defined in the schema.
		$value = WP_REST_Meta_Fields::prepare_value( $value, $request, $args );

		// Render dynamic variables.
		$value = array_map( [ Helper::class, 'render' ], $value );

		return $value;
	}

	private function get_post_meta_title(): string {
		$post = get_post();
		if ( empty( $post ) ) {
			return '';
		}

		$title = $this->title->get_singular_value( $post->ID );
		$title = $title ?: '{{ post.title }} {{ page }} {{ sep }} {{ site.title }}';
		$title = (string) apply_filters( 'slim_seo_meta_title', $title, $post->ID );
		$title = Helper::render( $title, $post->ID );

		return $title;
	}

	private function get_post_meta_description(): string {
		$post = get_post();
		if ( empty( $post ) ) {
			return '';
		}

		$description = $this->description->get_singular_value( $post->ID );
		$description = (string) apply_filters( 'slim_seo_meta_description', $description, $post->ID );
		$description = Helper::render( $description, $post->ID );

		return $description;
	}
}
