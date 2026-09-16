<?php
namespace SlimSEO\MetaTags\Abilities;

use SlimSEO\Abilities\Base as AbilitiesBase;
use SlimSEO\MetaTags\Helper as MetaTagsHelper;

class Base extends AbilitiesBase {
	protected $object_type = '';
	protected $object_id   = 0;

	protected function output_schema( bool $detailed = true ): array {
		$fields = [
			'title'          => __( 'Meta title.', 'slim-seo' ),
			'description'    => __( 'Meta description.', 'slim-seo' ),
			'facebook_image' => __( 'Open Graph image URL.', 'slim-seo' ),
			'x_image'        => __( 'X image URL.', 'slim-seo' ),
		];

		if ( 'settings' !== $this->object_type ) {
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

		$properties['noindex'] = [
			'type'        => 'boolean',
			'description' => __( 'Whether the post/page should be excluded from search engines.', 'slim-seo' ),
		];

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

		update_metadata( $this->object_type, $this->object_id, 'slim_seo', $data );
	}

	protected function sanitize_data( array $data, array $input ): array {
		$fields = [ 'title', 'description', 'facebook_image' ];

		if ( 'settings' !== $this->object_type ) {
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

		if ( isset( $input['noindex'] ) ) {
			$data['noindex'] = $input['noindex'] ? 1 : 0;
		}

		return array_filter( $data );
	}

	protected function normalize_data( array $data ): array {
		$data = [
			'title'          => $this->render_field( $data['title'] ?? '' ),
			'description'    => $this->render_field( $data['description'] ?? '' ),
			'facebook_image' => $this->render_field( $data['facebook_image'] ?? '' ),
			'x_image'        => $this->render_field( $data['twitter_image'] ?? '' ),
			'noindex'        => (int) ( $data['noindex'] ?? 0 ),
		];

		if ( 'settings' !== $this->object_type ) {
			$data['canonical'] = $this->render_field( $data['canonical'] ?? '' );
		}

		return $data;
	}

	private function render_field( string $value ): array {
		$post_id = 'post' === $this->object_type ? $this->object_id : 0;
		$term_id = 'term' === $this->object_type ? $this->object_id : 0;

		return [
			'raw'      => $value,
			'rendered' => MetaTagsHelper::render( $value, $post_id, $term_id ),
		];
	}
}
