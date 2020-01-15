<?php
namespace SlimSEO;

class Feed {
	public function setup() {
		add_filter( 'the_content_feed', [ $this, 'add_link' ] );
		if ( get_option( 'rss_use_excerpt' ) ) {
			add_filter( 'the_excerpt_rss', [ $this, 'add_link' ] );
		}
	}

	public function add_link( $content ) {
		$content .= "\n" . '<p><a href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'Source', 'slim-seo' ) . '</a></p>';
		return $content;
	}
}
