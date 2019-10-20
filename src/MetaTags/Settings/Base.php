<?php
namespace SlimSEO\MetaTags\Settings;

class Base {
	protected $defaults = [
		'title'       => '',
		'description' => '',
		'noindex'     => 0,
	];

	public function enqueue() {
		wp_enqueue_style( 'slim-seo-meta-box', SLIM_SEO_URL . 'css/meta-box.css', [], '3.0.0' );
	}

	public function render() {
		$data = $this->get_data();
		wp_nonce_field( 'save', 'ss_nonce' );
		?>
		<p class="ss-field">
			<label class="ss-label" for="ss-title"><?php esc_html_e( 'Meta title', 'slim-seo' ); ?></label>
			<input type="text" class="ss-text" id="ss-title" name="slim_seo[title]" value="<?= esc_attr( $data['title'] ); ?>">
		</p>
		<p class="ss-field">
			<label class="ss-label" for="ss-description"><?php esc_html_e( 'Meta description', 'slim-seo' ); ?></label>
			<input type="text" class="ss-text" id="ss-description" name="slim_seo[description]" value="<?= esc_attr( $data['description'] ); ?>">
		</p>
		<p class="ss-field">
			<label>
				<input type="checkbox" name="slim_seo[noindex]" value="1" <?php checked( $data['noindex'] ); ?>>
				<?php esc_html_e( 'Hide from search results', 'slim-seo' ); ?>
			</label>
		</p>
		<?php
	}

	public function get_form_data() {
		if ( ! check_ajax_referer( 'save', 'ss_nonce', false ) ) {
			return null;
		}
		$data = isset( $_POST['slim_seo'] ) ? $_POST['slim_seo'] : [];
		$data = array_map( 'sanitize_text_field', $data );

		return $data;
	}
}