<?php
namespace SlimSEO\Settings\Content;

class Homepage {
	private $defaults   = [
		'title'          => '',
		'description'    => '',
		'facebook_image' => '',
		'twitter_image'  => '',
	];

	private function get_default_title(): string {
		$parts = apply_filters( 'slim_seo_title_parts', [ 'site', 'tagline' ], 'post' );

		$values = [
			'site'    => get_bloginfo( 'name' ),
			'tagline' => get_bloginfo( 'description' ),
		];

		$parts = array_map( function ( string $part ) use ( $values ): string {
			return $values[ $part ] ?? '';
		}, $parts );

		$separator = apply_filters( 'document_title_separator', '-' ); // phpcs:ignore
		return implode( " $separator ", $parts );
	}

	private function get_default_description(): string {
		return get_bloginfo( 'description' ) ?? '';
	}

	public function sanitize( array $data ): array {
		$data = array_merge( $this->defaults, $data );

		$data['title']          = sanitize_text_field( $data['title'] );
		$data['description']    = sanitize_text_field( $data['description'] );
		$data['facebook_image'] = sanitize_text_field( $data['facebook_image'] );
		$data['twitter_image']  = sanitize_text_field( $data['twitter_image'] );

		return array_filter( $data );
	}

	public function get_data(): array {
		return array_merge( $this->defaults, [
			'title' => $this->get_default_title(),
			'description' => $this->get_default_description(),
		] );
	}
}
