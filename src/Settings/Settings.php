<?php
namespace SlimSEO\Settings;

class Settings {
	private $defaults = [
		'header_code'         => '',
		'body_code'           => '',
		'footer_code'         => '',

		'home_title'          => '',
		'home_description'    => '',
		'home_facebook_image' => '',
		'home_twitter_image'  => '',
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

		if ( ! $this->is_static_homepage() ) {
			wp_enqueue_media();
			wp_enqueue_style( 'slim-seo-meta-box', SLIM_SEO_URL . 'css/meta-box.css', [], SLIM_SEO_VER );
			wp_enqueue_script( 'slim-seo-meta-box', SLIM_SEO_URL . 'js/meta-box.js', ['jquery', 'underscore'], SLIM_SEO_VER, true );
			$params = [
				'site' => [
					'title'       => get_bloginfo( 'name' ),
					'description' => get_bloginfo( 'description' ),
				],
				'mediaPopupTitle' => __( 'Select An Image', 'slim-seo' ),
			];
			wp_localize_script( 'slim-seo-meta-box', 'ss', $params );
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
				<?php if ( ! $this->is_static_homepage() ) : ?>
					<a href="#homepage" class="nav-tab"><?php esc_html_e( 'Homepage', 'slim-seo' ); ?></a>
				<?php endif; ?>
				<a href="#tools" class="nav-tab"><?php esc_html_e( 'Tools', 'slim-seo' ); ?></a>
			</h2>

			<form action="" method="post">
				<?php
				wp_nonce_field( 'save' );

				include __DIR__ . '/sections/general.php';
				if ( ! $this->is_static_homepage() ) {
					include __DIR__ . '/sections/homepage.php';
				}
				include __DIR__ . '/sections/tools.php';
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
		$option = $option ? $option : [];
		$option = array_merge( $option, $data );

		update_option( 'slim_seo', $option );
	}

	private function sanitize( $data ) {
		$data = array_merge( $this->defaults, $data );

		$data['home_title']          = sanitize_text_field( $data['home_title'] );
		$data['home_description']    = sanitize_text_field( $data['home_description'] );
		$data['home_facebook_image'] = esc_url_raw( $data['home_facebook_image'] );
		$data['home_twitter_image']  = esc_url_raw( $data['home_twitter_image'] );

		return array_filter( $data );
	}

	private function is_static_homepage() {
		return 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' );
	}
}
