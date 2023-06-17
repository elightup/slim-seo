<?php
namespace SlimSEO\Settings;

class Settings {
	private $meta_tags_manager;

	private $defaults = [
		'header_code'            => '',
		'body_code'              => '',
		'footer_code'            => '',
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

	public function __construct( MetaTags\Manager $meta_tags_manager ) {
		$this->meta_tags_manager = $meta_tags_manager;
	}

	public function setup() {
		add_filter( 'slim_seo_settings_tabs', [ $this, 'add_tabs' ], 1 );
		add_filter( 'slim_seo_settings_panes', [ $this, 'add_panes' ], 1 );
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ], 1 );

		add_action( 'slim_seo_save', [ $this, 'save' ], 1 );
	}

	public function add_tabs( array $tabs ): array {
		$tabs['general'] = __( 'Features', 'slim-seo' );

		if ( $this->meta_tags_manager->has_homepage_settings() ) {
			$tabs['homepage'] = __( 'Homepage', 'slim-seo' );
		}
		if ( $this->meta_tags_manager->get_post_types() ) {
			$tabs['post-types'] = __( 'Post Types', 'slim-seo' );
		}

		$tabs['social'] = __( 'Social', 'slim-seo' );
		$tabs['tools']  = __( 'Tools', 'slim-seo' );
		return $tabs;
	}

	public function add_panes( array $panes ): array {
		$panes['general'] = $this->get_pane( 'general' );

		if ( $this->meta_tags_manager->has_homepage_settings() ) {
			$panes['homepage'] = $this->get_pane( 'homepage' );
		}
		if ( $this->meta_tags_manager->get_post_types() ) {
			$panes['post-types'] = $this->get_pane( 'post-types' );
		}

		$panes['social'] = $this->get_pane( 'social' );
		$panes['tools']  = $this->get_pane( 'tools' );
		return $panes;
	}

	public function enqueue() {
		wp_enqueue_style( 'slim-seo-settings', SLIM_SEO_URL . 'css/settings.css', [], filemtime( SLIM_SEO_DIR . '/css/settings.css' ) );
		wp_enqueue_script( 'slim-seo-settings', SLIM_SEO_URL . 'js/settings.js', [], filemtime( SLIM_SEO_DIR . '/js/settings.js' ), true );

		wp_enqueue_script( 'slim-seo-migrate', SLIM_SEO_URL . 'js/migrate.js', [], filemtime( SLIM_SEO_DIR . '/js/migrate.js' ), true );
		wp_localize_script( 'slim-seo-migrate', 'ssMigration', [
			'nonce'          => wp_create_nonce( 'migrate' ),
			'doneText'       => __( 'Done!', 'slim-seo' ),
			'preProcessText' => __( 'Starting...', 'slim-seo' ),
		] );

		$this->meta_tags_manager->enqueue();
	}

	public function save() {
		// @codingStandardsIgnoreLine.
		$data = isset( $_POST['slim_seo'] ) ? wp_unslash( $_POST['slim_seo'] ) : [];

		$option = get_option( 'slim_seo' );
		$option = $option ?: [];
		$option = array_merge( $option, $data );
		$option = apply_filters( 'slim_seo_option', $option, $data );
		$option = $this->sanitize( $option, $data );

		if ( empty( $option ) ) {
			delete_option( 'slim_seo' );
		} else {
			update_option( 'slim_seo', $option );
		}
	}

	private function sanitize( $option, $data ) {
		$option = array_merge( $this->defaults, $option );

		$this->meta_tags_manager->sanitize( $option, $data );

		return array_filter( $option );
	}

	public function is_feature_active( $feature ) {
		$defaults = $this->defaults['features'];
		$option   = get_option( 'slim_seo' );
		$features = $option['features'] ?? $defaults;

		return in_array( $feature, $features, true ) || ! in_array( $feature, $defaults, true );
	}

	public function get_pane( string $name ): string {
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
