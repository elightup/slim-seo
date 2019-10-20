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
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-title"><?php esc_html_e( 'Meta title', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<input type="text" id="ss-title" name="slim_seo[title]" value="<?= esc_attr( $data['title'] ); ?>">
			</div>
		</div>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-description"><?php esc_html_e( 'Meta description', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<textarea id="ss-description" name="slim_seo[description]" rows="3"><?= esc_textarea( $data['description'] ); ?></textarea>
			</div>
		</div>
		<div class="ss-field ss-field-checkbox">
			<div class="ss-label">
				<label for="ss-noindex"><?php esc_html_e( 'Hide from search results', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<input type="checkbox" id="ss-noindex" name="slim_seo[noindex]" value="1" <?php checked( $data['noindex'] ); ?>>
			</div>
		</div>
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