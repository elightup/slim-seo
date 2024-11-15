<?php
namespace SlimSEO;

use SlimSEO\Helpers\Assets;

class LinkAttributes {
	public function setup(): void {
		// Replace default WordPress wplink (classic editor) script.
		add_action( 'wp_enqueue_editor', [ $this, 'enqueue_for_classic_editor' ] );
		// Replace default WordPress link (block editor)
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_for_block_editor' ] );
	}

	public function enqueue_for_classic_editor(): void {
		wp_deregister_script( 'wplink' );

		wp_enqueue_script(
			'wplink',
			SLIM_SEO_URL . 'js/link-attributes/classic-editor.js',
			[ 'jquery', 'wp-a11y' ],
			filemtime( SLIM_SEO_DIR . 'js/link-attributes/classic-editor.js' ),
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

	public function enqueue_for_block_editor() {
		$wp_version = get_bloginfo( 'version' );

		if ( version_compare( $wp_version, '6.3', '<' ) ) {
			return;
		}

		if (
			version_compare( $wp_version, '6.6', '<' )
			&& file_exists( ABSPATH . 'wp-includes/js/dist/vendor/react-jsx-runtime.min.js' )
		) {
			wp_enqueue_script( 'react-jsx-runtime', includes_url( 'js/dist/vendor/react-jsx-runtime.min.js' ), [ 'react' ] ); // phpcs:ignore
		}

		wp_enqueue_style( 'slim-seo-link-attributes', SLIM_SEO_URL . 'css/link-attributes.css', [], filemtime( SLIM_SEO_DIR . '/css/link-attributes.css' ) );
		Assets::enqueue_build_js( 'link-attributes', 'SSLinkAttributes', [
			'wpVersion' => $wp_version,
		] );
	}
}
