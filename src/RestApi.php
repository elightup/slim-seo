<?php
namespace SlimSEO;

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
			],
		];

		register_meta( 'post', 'slim_seo', $args );
		register_meta( 'term', 'slim_seo', $args );
	}
}
