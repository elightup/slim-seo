<?php
namespace SlimSEO\MetaTags\Abilities;

use SlimSEO\Abilities\Base as AbilitiesBase;
use SlimSEO\MetaTags\Helper as MetaTagsHelper;
use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Description;

abstract class Base extends AbilitiesBase {
	protected $object_type   = 'post';
	protected $object_id     = 0;
	protected $has_canonical = true;
	protected $has_noindex   = true;

	public function register_abilities(): void {
		$config = $this->ability_config();

		wp_register_ability( "slim-seo/get-{$config['slug']}-meta-tags", [
			'category'            => 'slim-seo',
			'label'               => $config['get_label'],
			'description'         => $config['get_description'],
			'input_schema'        => $this->input_schema(),
			'output_schema'       => $this->output_schema(),
			'meta'                => $this->meta(),
			'permission_callback' => function ( array $input ) {
				return $this->check_permission( $input );
			},
			'execute_callback'    => function ( array $input ) {
				return $this->get_data( $input );
			},
		] );

		wp_register_ability( "slim-seo/update-{$config['slug']}-meta-tags", [
			'category'            => 'slim-seo',
			'label'               => $config['update_label'],
			'description'         => $config['update_description'],
			'input_schema'        => $this->input_schema( false ),
			'output_schema'       => $this->success_schema(),
			'meta'                => $this->meta( false ),
			'permission_callback' => function ( array $input ) {
				return $this->check_permission( $input );
			},
			'execute_callback'    => function ( array $input ) {
				return $this->update_data( $input );
			},
		] );
	}

	abstract protected function ability_config(): array;
	abstract protected function get_data( array $input );
	abstract protected function update_data( array $input );

	protected function output_schema( bool $detailed = true ): array {
		$fields = [
			'title'          => __( 'Meta title.', 'slim-seo' ),
			'description'    => __( 'Meta description.', 'slim-seo' ),
			'facebook_image' => __( 'Open Graph image URL.', 'slim-seo' ),
			'x_image'        => __( 'X image URL.', 'slim-seo' ),
		];

		if ( $this->has_canonical ) {
			$fields['canonical'] = __( 'Canonical URL.', 'slim-seo' );
		}

		$properties = [];

		foreach ( $fields as $key => $description ) {
			$property = [
				'type'        => $detailed ? 'object' : 'string',
				'description' => $description,
			];

			if ( $detailed ) {
				$property['properties']           = [
					'raw'      => [
						'type'        => 'string',
						'description' => __( 'Raw value with dynamic variables.', 'slim-seo' ),
					],
					'rendered' => [
						'type'        => 'string',
						'description' => __( 'Rendered value with variables replaced.', 'slim-seo' ),
					],
				];
				$property['additionalProperties'] = false;
			}

			$properties[ $key ] = $property;
		}

		if ( $this->has_noindex ) {
			$properties['noindex'] = [
				'type'        => 'boolean',
				'description' => __( 'Whether the post/page should be excluded from search engines.', 'slim-seo' ),
			];
		}

		return $detailed
			? [
				'type'                 => 'object',
				'properties'           => $properties,
				'additionalProperties' => false,
			]
			: $properties;
	}

	protected function get_object_data(): array {
		return get_metadata( $this->object_type, $this->object_id, 'slim_seo', true ) ?: [];
	}

	protected function update_object_data( array $input ): void {
		$data = $this->get_object_data();
		$data = $this->sanitize_data( $data, $input );

		if ( empty( $data ) ) {
			delete_metadata( $this->object_type, $this->object_id, 'slim_seo' );
		} else {
			update_metadata( $this->object_type, $this->object_id, 'slim_seo', $data );
		}
	}

	protected function default_data( string $type = '' ): array {
		$type = $type ? $type : $this->object_type;

		return [
			'title'       => $type ? ( Title::DEFAULTS[ $type ] ?? '' ) : '',
			'description' => $type ? ( Description::DEFAULTS[ $type ] ?? '' ) : '',
		];
	}

	private function render_field( string $value ): array {
		$post_id = 'post' === $this->object_type ? $this->object_id : 0;
		$term_id = 'term' === $this->object_type ? $this->object_id : 0;

		return [
			'raw'      => $value,
			'rendered' => MetaTagsHelper::render( $value, $post_id, $term_id ),
		];
	}

	protected function normalize_data( array $data, string $type = '' ): array {
		$default_data = $this->default_data( $type );
		$new_data     = [
			'title'          => $this->render_field( $data['title'] ?? $default_data['title'] ),
			'description'    => $this->render_field( $data['description'] ?? $default_data['description'] ),
			'facebook_image' => $this->render_field( $data['facebook_image'] ?? '' ),
			'x_image'        => $this->render_field( $data['twitter_image'] ?? '' ),
		];

		if ( $this->has_noindex ) {
			$new_data['noindex'] = (bool) ( $data['noindex'] ?? 0 );
		}

		if ( $this->has_canonical ) {
			$new_data['canonical'] = $this->render_field( $data['canonical'] ?? '' );
		}

		return $new_data;
	}

	protected function sanitize_data( array $data, array $input ): array {
		$fields = [ 'title', 'description', 'facebook_image' ];

		if ( $this->has_canonical ) {
			$fields[] = 'canonical';
		}

		foreach ( $fields as $field ) {
			if ( ! isset( $input[ $field ] ) ) {
				continue;
			}

			$data[ $field ] = sanitize_text_field( $input[ $field ] );
		}

		if ( isset( $input['x_image'] ) ) {
			$data['twitter_image'] = sanitize_text_field( $input['x_image'] );
		}

		if ( $this->has_noindex && isset( $input['noindex'] ) ) {
			$data['noindex'] = $input['noindex'] ? 1 : 0;
		}

		return array_filter( $data, fn( $v ) => '' !== $v && null !== $v );
	}
}
