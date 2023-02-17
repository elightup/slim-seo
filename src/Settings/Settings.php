<?php
namespace SlimSEO\Settings;

class Settings {
	private $defaults = [
		'header_code'            => '',
		'body_code'              => '',
		'footer_code'            => '',
		'home_title'             => '',
		'home_description'       => '',
		'home_facebook_image'    => '',
		'home_twitter_image'     => '',
		'default_facebook_image' => '',
		'default_twitter_image'  => '',
		'facebook_app_id'        => '',
		'twitter_site'           => '',
		'features'               => [
			'meta_title',
			'meta_description',
			'meta_robots',
			'open_graph',
			'twitter_cards',
			'canonical_url',
			'rel_links',
			'sitemaps',
			'images_alt',
			'breadcrumbs',
			'feed',
			'schema',
			'redirection',
		],
	];

	public function setup() {
		add_filter( 'slim_seo_settings_tabs', [ $this, 'add_tab' ], 1 );
		add_filter( 'slim_seo_settings_panes', [ $this, 'add_pane' ], 1 );
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ], 1 );

		add_action( 'slim_seo_save', [ $this, 'save' ], 1 );
	}

	public function add_tab( array $tabs ) : array {
		$tabs['general'] = __( 'Features', 'slim-seo' );
		if ( ! $this->is_static_homepage() ) {
			$tabs['homepage'] = __( 'Homepage', 'slim-seo' );
		}
		$tabs['social'] = __( 'Social', 'slim-seo' );
		$tabs['tools']  = __( 'Tools', 'slim-seo' );
		return $tabs;
	}

	public function add_pane( array $panes ) : array {
		$panes['general'] = $this->get_pane( 'general' );
		if ( ! $this->is_static_homepage() ) {
			$panes['homepage'] = $this->get_pane( 'homepage' );
		}
		$panes['social'] = $this->get_pane( 'social' );
		$panes['tools']  = $this->get_pane( 'tools' );
		return $panes;
	}

	public function enqueue() {
		wp_register_script( 'tippy', 'https://cdn.jsdelivr.net/combine/npm/@popperjs/core@2.11.2/dist/umd/popper.min.js,npm/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js', [], '6.3.7', true );

		wp_enqueue_style( 'slim-seo-settings', SLIM_SEO_URL . 'css/settings.css', [], SLIM_SEO_VER );
		wp_enqueue_script( 'slim-seo-settings', SLIM_SEO_URL . 'js/settings.js', [ 'tippy' ], SLIM_SEO_VER, true );

		wp_enqueue_script( 'slim-seo-migrate', SLIM_SEO_URL . 'js/migrate.js', [], SLIM_SEO_VER, true );
		wp_localize_script( 'slim-seo-migrate', 'ssMigration', [
			'nonce'          => wp_create_nonce( 'migrate' ),
			'doneText'       => __( 'Done!', 'slim-seo' ),
			'preProcessText' => __( 'Starting...', 'slim-seo' ),
		] );

		wp_enqueue_media();
		$params = [
			'mediaPopupTitle' => __( 'Select An Image', 'slim-seo' ),
		];
		wp_enqueue_style( 'slim-seo-meta-box', SLIM_SEO_URL . 'css/meta-box.css', [], SLIM_SEO_VER );
		if ( ! $this->is_static_homepage() ) {
			wp_enqueue_script( 'slim-seo-meta-box', SLIM_SEO_URL . 'js/meta-box.js', [ 'jquery', 'underscore' ], SLIM_SEO_VER, true );
			$params['site']  = [
				'title'       => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ),
				'description' => html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES, 'UTF-8' ),
			];
			$params['title'] = [
				'separator' => apply_filters( 'document_title_separator', '-' ),
				'parts'     => apply_filters( 'slim_seo_title_parts', [ 'site', 'tagline' ], 'home' ),
			];

			wp_localize_script( 'slim-seo-meta-box', 'ss', $params );
		} else {
			wp_enqueue_script( 'slim-seo-media', SLIM_SEO_URL . 'js/media.js', [], SLIM_SEO_VER, true );
			wp_localize_script( 'slim-seo-media', 'ss', $params );
		}
	}

	public function save() {
		// @codingStandardsIgnoreLine.
		$data = isset( $_POST['slim_seo'] ) ? wp_unslash( $_POST['slim_seo'] ) : [];

		$option = get_option( 'slim_seo' );
		$option = $option ?: [];
		$option = array_merge( $option, $data );
		$option = apply_filters( 'slim_seo_option', $option, $data );
		$option = $this->sanitize( $option );

		if ( empty( $option ) ) {
			delete_option( 'slim_seo' );
		} else {
			update_option( 'slim_seo', $option );
		}
	}

	private function sanitize( $option ) {
		$option = array_merge( $this->defaults, $option );

		$option['home_title']          = sanitize_text_field( $option['home_title'] );
		$option['home_description']    = sanitize_text_field( $option['home_description'] );
		$option['home_facebook_image'] = esc_url_raw( $option['home_facebook_image'] );
		$option['home_twitter_image']  = esc_url_raw( $option['home_twitter_image'] );

		return array_filter( $option );
	}

	private function is_static_homepage() {
		return 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' );
	}

	public function is_feature_active( $feature ) {
		$defaults = $this->defaults['features'];
		$data     = get_option( 'slim_seo' );
		$features = $data['features'] ?? $defaults;

		return in_array( $feature, $features, true ) || ! in_array( $feature, $defaults, true );
	}

	public function tooltip( $content ) {
		echo '<button type="button" class="ss-tooltip" data-tippy-content="', esc_attr( $content ), '"><span class="dashicons dashicons-editor-help"></span></button>';
	}

	public function get_pane( string $name ) : string {
		$data = get_option( 'slim_seo' );
		$data = $data ? $data : [];
		$data = array_merge( $this->defaults, $data );

		ob_start();
		echo '<div id="', esc_attr( $name ), '" class="ss-tab-pane">';
		include __DIR__ . "/sections/$name.php";
		echo '</div>';
		return ob_get_clean();
	}
}
