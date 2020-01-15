<?php
namespace SlimSEO;

class ImagesAlt {
	public function setup() {
		// Add missing alt attribute when outputing images via the_post_thumbnail-family functions.
		add_filter( 'wp_get_attachment_image_attributes', [ $this, 'add_missing_alt_attribute' ], 10, 2 );

		// Add missing alt attribute when inserting images to the editor. Work with both classic and Gutenberg editor.
		add_filter( 'wp_prepare_attachment_for_js', [ $this, 'add_missing_alt_attribute' ], 10, 2 );
	}

	public function add_missing_alt_attribute( $attr, $attachment ) {
		$attr['alt'] = $attr['alt'] ?: $attachment->post_title;
		return $attr;
	}
}
