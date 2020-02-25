<?php
namespace SlimSEO\Settings;

class Settings {
	public function setup() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	public function add_menu() {
		$page_hook = add_options_page(
			__( 'SEO', 'slim-seo' ),
			__( 'SEO', 'slim-seo' ),
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
	}

	public function render() {
		$data = get_option( 'slim_seo' );
		$data = $data ? $data : [];
		$data = array_merge( [
			'header_code' => '',
			'footer_code' => '',
		], $data );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'SEO Settings', 'slim-seo' ); ?></h1>
			<?php
			include __DIR__ . '/sections/tabs.php';
			include __DIR__ . '/sections/general.php';
			include __DIR__ . '/sections/tools.php';
			?>
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
}
