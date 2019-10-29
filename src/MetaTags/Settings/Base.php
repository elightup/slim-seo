<?php
namespace SlimSEO\MetaTags\Settings;

abstract class Base {
	protected $object_type;
	protected $title;
	protected $defaults = [
		'title'       => '',
		'description' => '',
		'noindex'     => 0,
	];

	public function enqueue() {
		wp_enqueue_style( 'slim-seo-meta-box', SLIM_SEO_URL . 'css/meta-box.css', [], SLIM_SEO_VER );
		wp_enqueue_script( 'slim-seo-meta-box', SLIM_SEO_URL . 'js/meta-box.js', ['jquery'], SLIM_SEO_VER, true );
		wp_localize_script( 'slim-seo-meta-box', 'ss', [
			'site' => ['title' => get_bloginfo( 'name' )],
		] );
	}

	public function render() {
		$data = $this->get_data();
		wp_nonce_field( 'save', 'ss_nonce' );
		?>

		<?php if ( $this->title ) : ?>
			<h2><?= esc_html( $this->title ); ?></h2>
		<?php endif; ?>

		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-title"><?php esc_html_e( 'Meta title', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<input type="text" id="ss-title" name="slim_seo[title]" value="<?= esc_attr( $data['title'] ); ?>">
				<div class="ss-count">
					<?php esc_html_e( 'Character count:', 'slim-seo' ); ?>
					<span class="ss-number">0</span>.
					<?php esc_html_e( 'Recommended length: ≤ 60 characters. ', 'slim-seo' ); ?>
				</div>
			</div>
		</div>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-description"><?php esc_html_e( 'Meta description', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<textarea id="ss-description" name="slim_seo[description]" rows="3"><?= esc_textarea( $data['description'] ); ?></textarea>
				<div class="ss-count">
					<?php esc_html_e( 'Character count:', 'slim-seo' ); ?>
					<span class="ss-number">0</span>.
					<?php esc_html_e( 'Recommended length: 50-160 characters. ', 'slim-seo' ); ?>
				</div>
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

	public function save( $object_id ) {
		if ( ! check_ajax_referer( 'save', 'ss_nonce', false ) ) {
			return;
		}
		$data = isset( $_POST['slim_seo'] ) ? $_POST['slim_seo'] : [];
		$data = array_map( 'sanitize_text_field', $data );

		update_metadata( $this->object_type, $object_id, 'slim_seo', $data );
	}

	protected function get_data() {
		$data = get_metadata( $this->object_type, $this->get_object_id(), 'slim_seo', true );
		$data = $data ? $data : [];

		return array_merge( $this->defaults, $data );
	}
}