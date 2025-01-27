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
		$post = get_post();
		if ( ! $post ) {
			return [];
		}

		$parsed_value = [
			'title'       => $this->title->get_rendered_singular_value( $post->ID ),
			'description' => $this->description->get_rendered_singular_value( $post->ID ),
		];
		$value = array_merge( (array) $value, $parsed_value );

		// Let WordPress prepare the value first. It will auto format the value as defined in the schema.
		return WP_REST_Meta_Fields::prepare_value( $value, $request, $args );
	}

	public function prepare_value_for_term( $value, WP_REST_Request $request, array $args ): array {
		// Let WordPress prepare the value first. It will auto format the value as defined in the schema.
		$value = WP_REST_Meta_Fields::prepare_value( $value, $request, $args );

		// Render dynamic variables.
		$value = array_map( [ Helper::class, 'render' ], $value );

		return $value;
	}
}
