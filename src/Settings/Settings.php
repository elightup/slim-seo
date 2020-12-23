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
			'cleaner',
			'schema',
		],
	];

	public function setup() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	public function add_menu() {
		$page_hook = add_options_page(
			__( 'Slim SEO', 'slim-seo' ),
			__( 'Slim SEO', 'slim-seo' ),
			'manage_options',
			'slim-seo',
			[ $this, 'render' ]
		);
		add_action( "load-{$page_hook}", [ $this, 'save' ] );
		add_action( "admin_print_styles-{$page_hook}", [ $this, 'enqueue' ] );
	}

	public function enqueue() {
		wp_enqueue_script( 'slim-seo-migrate-js', SLIM_SEO_URL . 'js/migrate.js', [], SLIM_SEO_VER, true );
		wp_enqueue_script( 'slim-seo-settings-js', SLIM_SEO_URL . 'js/settings.js', [], SLIM_SEO_VER, true );
		wp_enqueue_style( 'slim-seo-migrate-css', SLIM_SEO_URL . 'css/settings.css' );
		wp_localize_script( 'slim-seo-migrate-js', 'ssMigration', [
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
			$params[ 'site' ] = [
				'title'       => get_bloginfo( 'name' ),
				'description' => get_bloginfo( 'description' ),
			];
			wp_localize_script( 'slim-seo-meta-box', 'ss', $params );
		} else {
			wp_enqueue_script( 'slim-seo-media', SLIM_SEO_URL . 'js/media.js', [], SLIM_SEO_VER, true );
			wp_localize_script( 'slim-seo-media', 'ss', $params );
		}
	}

	public function render() {
		$data = get_option( 'slim_seo' );
		$data = $data ? $data : [];
		$data = array_merge( $this->defaults, $data );
		?>
		<div class="wrap">
			<h1><?= esc_html( get_admin_page_title() ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a href="#general" class="nav-tab nav-tab-active"><?php esc_html_e( 'General', 'slim-seo' ); ?></a>
				<a href="#code" class="nav-tab"><?php esc_html_e( 'Code', 'slim-seo' ); ?></a>
				<?php if ( ! $this->is_static_homepage() ) : ?>
					<a href="#homepage" class="nav-tab"><?php esc_html_e( 'Homepage', 'slim-seo' ); ?></a>
				<?php endif; ?>
				<a href="#social" class="nav-tab"><?php esc_html_e( 'Social', 'slim-seo' ); ?></a>
				<a href="#tools" class="nav-tab"><?php esc_html_e( 'Tools', 'slim-seo' ); ?></a>
				<?php do_action( 'slim_seo_settings_tabs' ); ?>
			</h2>

			<form action="" method="post">
				<?php
				wp_nonce_field( 'save' );

				include __DIR__ . '/sections/general.php';
				include __DIR__ . '/sections/code.php';
				if ( ! $this->is_static_homepage() ) {
					include __DIR__ . '/sections/homepage.php';
				}
				include __DIR__ . '/sections/tools.php';
				include __DIR__ . '/sections/social.php';
				do_action( 'slim_seo_settings_panels' );
				?>
			</form>
		</div>
		<?php
	}

	public function save() {
		if ( empty( $_POST['submit'] ) || ! check_ajax_referer( 'save', false, false ) ) {
			return;
		}

		$data = isset( $_POST['slim_seo'] ) ? $_POST['slim_seo'] : [];
		$data = wp_unslash( $data );

		$option = get_option( 'slim_seo' );
		$option = $option ?: [];
		$option = array_merge( $option, $data );
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
		$features = isset( $data['features'] ) ? $data['features'] : $defaults;

		return in_array( $feature, $features, true ) || ! in_array( $feature, $defaults, true );
	}
}
