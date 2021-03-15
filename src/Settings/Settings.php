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
			'auto_redirection',
			'feed',
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
		wp_register_script( 'popper', SLIM_SEO_URL . 'js/popper.min.js', [], '2.9.1', true );
		wp_register_script( 'tippy', SLIM_SEO_URL . 'js/tippy-bundle.umd.min.js', ['popper'], '6.3.1', true );

		wp_enqueue_script( 'slim-seo-migrate-js', SLIM_SEO_URL . 'js/migrate.js', [], SLIM_SEO_VER, true );
		wp_enqueue_script( 'slim-seo-settings-js', SLIM_SEO_URL . 'js/settings.js', ['tippy'], SLIM_SEO_VER, true );
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
			<h1 class="ss-title">
				<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M472 0H40C17.9086 0 0 17.9086 0 40V472C0 494.091 17.9086 512 40 512H472C494.091 512 512 494.091 512 472V40C512 17.9086 494.091 0 472 0Z" fill="url(#paint0_linear)"/><path d="M259.353 398.8C238.82 398.8 220.42 395.467 204.153 388.8C187.886 382.133 174.82 372.267 164.953 359.2C155.353 346.133 150.286 330.4 149.753 312H222.553C223.62 322.4 227.22 330.4 233.353 336C239.486 341.333 247.486 344 257.353 344C267.486 344 275.486 341.733 281.353 337.2C287.22 332.4 290.153 325.867 290.153 317.6C290.153 310.667 287.753 304.933 282.953 300.4C278.42 295.867 272.686 292.133 265.753 289.2C259.086 286.267 249.486 282.933 236.953 279.2C218.82 273.6 204.02 268 192.553 262.4C181.086 256.8 171.22 248.533 162.953 237.6C154.686 226.667 150.553 212.4 150.553 194.8C150.553 168.667 160.02 148.267 178.953 133.6C197.886 118.667 222.553 111.2 252.953 111.2C283.886 111.2 308.82 118.667 327.753 133.6C346.686 148.267 356.82 168.8 358.153 195.2H284.153C283.62 186.133 280.286 179.067 274.153 174C268.02 168.667 260.153 166 250.553 166C242.286 166 235.62 168.267 230.553 172.8C225.486 177.067 222.953 183.333 222.953 191.6C222.953 200.667 227.22 207.733 235.753 212.8C244.286 217.867 257.62 223.333 275.753 229.2C293.886 235.333 308.553 241.2 319.753 246.8C331.22 252.4 341.086 260.533 349.353 271.2C357.62 281.867 361.753 295.6 361.753 312.4C361.753 328.4 357.62 342.933 349.353 356C341.353 369.067 329.62 379.467 314.153 387.2C298.686 394.933 280.42 398.8 259.353 398.8Z" fill="#fff"/><defs><linearGradient id="paint0_linear" x1="0" y1="0" x2="512" y2="512" gradientUnits="userSpaceOnUse"><stop stop-color="#C21500"/><stop offset="1" stop-color="#FFC500"/></linearGradient></defs></svg>
				<?= esc_html( get_admin_page_title() ); ?>
				<a href="https://wpslimseo.com/docs/" target="_blank" rel="noreffer noopener">
					<span class="dashicons dashicons-media-document"></span>
					<?php esc_html_e( 'Documentation', 'slim-seo' ) ?>
				</a>
			</h1>

			<form action="" method="post" class="ss-tabs">
				<nav class="ss-tab-list">
					<a href="#general" class="ss-tab ss-is-active"><?php esc_html_e( 'Features', 'slim-seo' ); ?></a>
					<a href="#code" class="ss-tab"><?php esc_html_e( 'Code', 'slim-seo' ); ?></a>
					<?php if ( ! $this->is_static_homepage() ) : ?>
						<a href="#homepage" class="ss-tab"><?php esc_html_e( 'Homepage', 'slim-seo' ); ?></a>
					<?php endif; ?>
					<a href="#social" class="ss-tab"><?php esc_html_e( 'Social', 'slim-seo' ); ?></a>
					<a href="#tools" class="ss-tab"><?php esc_html_e( 'Tools', 'slim-seo' ); ?></a>
					<?php do_action( 'slim_seo_settings_tabs' ); ?>
				</nav>
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

		add_settings_error( null, 'slim-seo', __( 'Settings updated.', 'slim-seo' ), 'success' );
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

	public function tooltip( $content ) {
		return '<button type="button" class="ss-tooltip" data-tippy-content="' . esc_attr( $content ) . '"><span class="dashicons dashicons-editor-help"></span></button>';
	}
}
