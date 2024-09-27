<?php
namespace SlimSEO\Settings\Content;

class Item {
	private $option_key = '';
	private $defaults   = [
		'title'          => '',
		'description'    => '',
		'facebook_image' => '',
		'twitter_image'  => '',
	];

	public function __construct( string $option_key ) {
		$this->option_key = $option_key;
	}

	public function sanitize( array $data ): array {
		$data = array_merge( $this->defaults, $data );

		$data['title']          = sanitize_text_field( $data['title'] );
		$data['description']    = sanitize_text_field( $data['description'] );
		$data['facebook_image'] = sanitize_text_field( $data['facebook_image'] );
		$data['twitter_image']  = sanitize_text_field( $data['twitter_image'] );

		return array_filter( $data );
	}

	public function get_home_data(): array {
		return array_merge( $this->defaults, [
			'link'        => get_home_url(),
			'name'        => get_the_title( get_option('page_on_front') ),
			'title'       => '{{ site.title }} {{ sep }} {{ site.description }}',
			'description' => '{{ site.description }}',
			'edit'        => get_edit_post_link( get_option('page_on_front') ),
		] );
	}
}
