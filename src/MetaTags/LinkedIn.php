<?php
namespace SlimSEO\MetaTags;

class LinkedIn {
	private $title;
	private $image_obj;

	public function __construct( Title $title ) {
		$this->title     = $title;
		$this->image_obj = new Image( 'linkedin_image' );
	}

	public function setup() {
		add_action( 'wp_head', [ $this, 'output' ] );
	}

	/**
	 *  How to specify a standard image and title.
	 *
	 * @link https://www.linkedin.com/advice/3/how-do-you-specify-images-media-social-sharing-html-skills-html
	 */
	public function output() {
		$title = $this->get_title();
		$title = apply_filters( 'slim_seo_linked_card_site', $title );
		if ( $title ) {
			echo '<meta name="title" content="' . esc_attr( $title ) . '">', "\n";
		}

		$image = $this->image_obj->get_value() ?: $this->get_default_image();
		$image = $image['src'] ?? '';
		$image = apply_filters( 'slim_seo_linked_card_image', $image );
		if ( ! empty( $image ) ) {
			echo '<meta name="image" content="' . esc_url( $image ) . '">', "\n";
		}
	}

	private function get_title(): string {
		return $this->title->get_title();
	}

	private function get_default_image(): array {
		$option = get_option( 'slim_seo' );
		return empty( $option['default_linkedin_image'] ) ? [] : $this->image_obj->get_data_from_url( $option['default_linkedin_image'] );
	}
}
