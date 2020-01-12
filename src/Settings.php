<?php
namespace SlimSEO;

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
			<h1><?= esc_html( get_admin_page_title() ); ?></h1>
			<form action="" method="post">
				<?php wp_nonce_field( 'save' ); ?>
				<p><?php esc_html_e( 'Use these settings to insert code from Google Tag Manager, Google Analytics or webmaster tools verification.', 'slim-seo' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="header-code"><?php esc_html_e( 'Header Code', 'slim-seo' ); ?></label>
						</th>
						<td>
							<textarea id="header-code" class="large-text" rows="10" name="slim_seo[header_code]"><?= esc_attr( $data['header_code'] ); ?></textarea>
							<p class="description"><?= wp_kses_post( __( 'Code entered in this box will be printed in the <code>&lt;head&gt;</code> section.', 'slim-seo' ) ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="footer-code"><?php esc_html_e( 'Footer Code', 'slim-seo' ); ?></label>
						</th>
						<td>
							<textarea id="footer-code" class="large-text" rows="10" name="slim_seo[footer_code]"><?= esc_attr( $data['footer_code'] ); ?></textarea>
							<p class="description"><?= wp_kses_post( __( 'Code entered in this box will be printed before the closing <code>&lt;/body&gt;</code> tag.', 'slim-seo' ) ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Save', 'slim-seo' ) ); ?>
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
}
