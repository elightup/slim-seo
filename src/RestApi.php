<?php
namespace SlimSEO;

use SlimSEO\MetaTags\Helper;
use WP_REST_Meta_Fields;
use WP_REST_Request;

class RestApi {
	public function setup() {
		add_action( 'rest_api_init', [ $this, 'register_meta' ] );
	}

	public function register_meta() {
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
				'prepare_callback' => [ $this, 'prepare_value' ],
			],
		];

		register_meta( 'post', 'slim_seo', $args );
		register_meta( 'term', 'slim_seo', $args );
	}

	public function prepare_value( $value, WP_REST_Request $request, array $args ): array {
		// Let WordPress prepare the value first. It will auto format the value as defined in the schema.
		$value = WP_REST_Meta_Fields::prepare_value( $value, $request, $args );

		// Render dynamic variables.
		$value = array_map( [ Helper::class, 'render' ], $value );

		return $value;
	}
}
