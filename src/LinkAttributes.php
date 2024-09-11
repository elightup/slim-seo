<?php
namespace SlimSEO;

class LinkAttributes {
	public function setup(): void {
		// Replace default WordPress wplink (classic editor) script.
		add_action( 'wp_enqueue_editor', [ $this, 'enqueue_for_classic_editor' ] );
	}

	public function enqueue_for_classic_editor(): void {
		wp_deregister_script( 'wplink' );

		wp_enqueue_script(
			'wplink',
			SLIM_SEO_URL . 'js/link-attributes.js',
			[ 'jquery', 'wp-a11y' ],
			filemtime( SLIM_SEO_DIR . 'js/link-attributes.js' ),
			true
		);

		wp_localize_script(
			'wplink',
			'ssLinkL10n',
			[
				// Existing wpLinkL10n.
				'title'          => esc_html__( 'Insert/edit link', 'slim-seo' ),
				'update'         => esc_html__( 'Update', 'slim-seo' ),
				'save'           => esc_html__( 'Add Link', 'slim-seo' ),
				'noTitle'        => esc_html__( '(no title)', 'slim-seo' ),
				'labelTitle'     => esc_html__( 'Title', 'slim-seo' ),
				'noMatchesFound' => esc_html__( 'No results found.', 'slim-seo' ),
				'linkSelected'   => esc_html__( 'Link selected.', 'slim-seo' ),
				'linkInserted'   => esc_html__( 'Link has been inserted.', 'slim-seo' ),
				// Translators: Minimum input length in characters to start searching posts in the "Insert/edit link" modal.
				'minInputLength' => (int) _x( '3', 'minimum input length for searching post links', 'slim-seo' ),

				// New strings.
				'nofollow'       => esc_html__( 'Add rel="nofollow" to link', 'slim-seo' ),
				'sponsored'      => esc_html__( 'Add rel="sponsored" to link', 'slim-seo' ),
				'ugc'            => esc_html__( 'Add rel="ugc" to link', 'slim-seo' ),
			]
		);
	}
}
