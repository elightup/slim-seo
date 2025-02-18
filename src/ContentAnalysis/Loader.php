<?php
namespace SlimSEO\ContentAnalysis;

use SlimSEO\Helpers\Assets;

class Loader {
	public function setup() {
		new Api;

		if ( ! is_admin() ) {
			return;
		}

		add_filter( 'slim_seo_metabox_tabs', [ $this, 'add_tab' ] );
		add_filter( 'slim_seo_metabox_panels', [ $this, 'add_panel' ] );
		add_filter( 'slim_seo_admin_columns', [ $this, 'admin_columns' ] );

		add_action( 'slim_seo_base_enqueue', [ $this, 'enqueue' ] );
		add_action( 'slim_seo_admin_column_render', [ $this, 'admin_column_render' ], 10, 2 );
	}

	public function add_tab( array $tabs ): array {
		$tabs['content-analysis'] = __( 'Writing assistant', 'slim-seo' );

		return $tabs;
	}

	public function add_panel( array $panels ): array {
		$panels['content-analysis'] = '<div id="content-analysis" class="ss-tab-pane"></div>';

		return $panels;
	}

	public function admin_columns( array $columns ): array {
		$columns['content_analysis'] = esc_html__( 'Writing assistant', 'slim-seo' );

		return $columns;
	}

	public function admin_column_render( $column, $post_id ) {
		if ( 'content_analysis' !== $column ) {
			return;
		}

		$data                  = get_post_meta( $post_id, 'slim_seo', true ) ?: [];
		$content_analysis_data = $data['content_analysis'] ?? [];

		if ( ! isset( $content_analysis_data['good_keywords'] ) ) {
			return;
		}

		$ok = ! empty( $content_analysis_data['good_keywords'] ) && ! empty( $content_analysis_data['good_words_count'] );

		if ( isset( $content_analysis_data['good_media'] ) ) {
			$ok = $ok && ! empty( $content_analysis_data['good_media'] );
		}

		echo '<span class="' . ( $ok ? 'ss-success' : 'ss-warning' ) . '"></span>';
	}

	public function enqueue() {
		wp_enqueue_script( 'slim-seo-settings', SLIM_SEO_URL . 'js/settings.js', [], filemtime( SLIM_SEO_DIR . '/js/settings.js' ), true );
		wp_enqueue_style( 'slim-seo-settings', SLIM_SEO_URL . 'css/settings.css', [], filemtime( SLIM_SEO_DIR . '/css/settings.css' ) );
		wp_enqueue_style( 'slim-seo-content-analysis', SLIM_SEO_URL . 'css/content-analysis.css', [], filemtime( SLIM_SEO_DIR . 'css/content-analysis.css' ) );

		$data                  = get_post_meta( get_the_ID(), 'slim_seo', true );
		$data                  = is_array( $data ) && ! empty( $data ) ? $data : [];
		$content_analysis_data = $data['content_analysis'] ?? [];

		Assets::enqueue_build_js( 'content-analysis', 'SSContentAnalysis', [
			'homeURL'       => untrailingslashit( home_url() ),
			'keywords'      => $content_analysis_data['keywords'] ?? '',
			'mainKeyword'   => $content_analysis_data['main_keyword'] ?? '',
			'SSLMActivated' => defined( 'SLIM_SEO_LINK_MANAGER_VER' ),
			'rest'          => untrailingslashit( rest_url() ),
			'nonce'         => wp_create_nonce( 'wp_rest' ),
		] );
	}
}
