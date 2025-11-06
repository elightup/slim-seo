<?php
namespace SlimSEO\Settings;

class Settings {
	private $meta_tags;

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
			'no_category_base',
		],
	];

	public function __construct() {
		$this->meta_tags = new MetaTags\Manager;
	}

	public function setup(): void {
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ], 1 );
		add_filter( 'slim_seo_settings_tabs', [ $this, 'add_tabs' ], 1 );
		add_filter( 'slim_seo_settings_panes', [ $this, 'add_panes' ], 1 );

		add_action( 'slim_seo_save', [ $this, 'save' ], 1 );
	}

	public function add_tabs( array $tabs ): array {
		$tabs['general']   = __( 'Features', 'slim-seo' );
		$tabs['meta-tags'] = __( 'Meta Tags', 'slim-seo' );
		$tabs['social']    = __( 'Social', 'slim-seo' );
		$tabs['tools']     = __( 'Tools', 'slim-seo' );
		return $tabs;
	}

	public function add_panes( array $panes ): array {
		$panes['general']   = $this->get_pane( 'general' );
		$panes['meta-tags'] = $this->get_pane( 'meta-tags' );
		$panes['social']    = $this->get_pane( 'social' );
		$panes['tools']     = $this->get_pane( 'tools' );
		return $panes;
	}

	public function enqueue(): void {
		$this->meta_tags->enqueue();

		wp_enqueue_script( 'slim-seo-migrate', SLIM_SEO_URL . 'js/migrate.js', [], filemtime( SLIM_SEO_DIR . '/js/migrate.js' ), true );
		wp_localize_script( 'slim-seo-migrate', 'ssMigration', [
			'nonce'          => wp_create_nonce( 'migrate' ),
			'doneText'       => __( 'Done!', 'slim-seo' ),
			'preProcessText' => __( 'Starting...', 'slim-seo' ),
		] );

		do_action( 'slim_seo_settings_enqueue' );
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

		$this->meta_tags->sanitize( $option, $data );

		return array_filter( $option );
	}

	public function is_feature_active( string $feature ): bool {
		$defaults = $this->defaults['features'];
		$option   = get_option( 'slim_seo' );
		$features = $option['features'] ?? $defaults;

		// Set features OFF by default
		if ( empty( $option['features'] ) ) {
			$features_off = [ 'no_category_base' ];
			$features     = array_diff( $features, $features_off );
		}

		return in_array( $feature, $features, true ) || ! in_array( $feature, $defaults, true );
	}

	public function get_pane( string $name ): string {
		$data = get_option( 'slim_seo' );
		$data = $data ? $data : [];
		$data = array_merge( $this->defaults, $data );

		ob_start();
		echo '<div id="', esc_attr( $name ), '" class="ss-tab-pane">';
		include __DIR__ . "/tabs/$name.php";
		echo '</div>';
		return ob_get_clean();
	}
}
