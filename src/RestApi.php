<?php
namespace SlimSEO;

class RestApi {
	public function setup() {
		add_action( 'rest_api_init', [ $this, 'register_posts_meta_field' ] );
	}

	public function register_posts_meta_field() {
		$object_type = 'post';
		$meta_args   = [ 
			'type'         => 'object',
			'description'  => '',
			'single'       => true,
			'show_in_rest' => [ 
				'schema' => [ 
					'type'       => 'object',
					'properties' => [ 
						'title'       => [ 
							'type' => 'string',
						],
						'description' => [ 
							'type' => 'string',
						],
					],
				],
			],
		];

		register_meta( $object_type, 'slim_seo', $meta_args );
	}
}