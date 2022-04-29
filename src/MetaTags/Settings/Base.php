<?php
namespace SlimSEO\MetaTags\Settings;

abstract class Base {
	protected $object_type;
	protected $title;
	protected $defaults = [
		'title'          => '',
		'description'    => '',
		'noindex'        => 0,
		'facebook_image' => '',
		'twitter_image'  => '',
	];

	public function enqueue() {
		wp_enqueue_media();
		wp_enqueue_style( 'slim-seo-meta-box', SLIM_SEO_URL . 'css/meta-box.css', [], SLIM_SEO_VER );
		wp_enqueue_script( 'slim-seo-meta-box', SLIM_SEO_URL . 'js/meta-box.js', [ 'jquery', 'underscore' ], SLIM_SEO_VER, true );
		$params = [
			'mediaPopupTitle' => __( 'Select An Image', 'slim-seo' ),
			'site'            => [
				'title'       => get_bloginfo( 'name' ),
				'description' => get_bloginfo( 'description' ),
			],
		];
		$params = array_merge( $params, $this->get_script_params() );
		wp_localize_script( 'slim-seo-meta-box', 'ss', $params );
	}

	protected function get_script_params() {
		return [];
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
				<p class="description">
					<?php esc_html_e( 'Character count:', 'slim-seo' ); ?>
					<span class="ss-counter">0</span>.
					<?php esc_html_e( 'Recommended length: ≤ 60 characters.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-description"><?php esc_html_e( 'Meta description', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<textarea id="ss-description" name="slim_seo[description]" rows="3"><?= esc_textarea( $data['description'] ); ?></textarea>
				<p class="description">
					<?php esc_html_e( 'Character count:', 'slim-seo' ); ?>
					<span class="ss-counter">0</span>.
					<?php esc_html_e( 'Recommended length: 50-160 characters.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-facebook-image"><?php esc_html_e( 'Facebook image', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<div class="ss-input-group">
					<input type="text" id="ss-facebook-image" name="slim_seo[facebook_image]" value="<?= esc_attr( $data['facebook_image'] ); ?>">
					<button class="ss-select-image button"><?php esc_html_e( 'Select image', 'slim-seo' ); ?></button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Recommended size: 1200x628 px.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-twitter-image"><?php esc_html_e( 'Twitter image', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<div class="ss-input-group">
					<input type="text" id="ss-twitter-image" name="slim_seo[twitter_image]" value="<?= esc_attr( $data['twitter_image'] ); ?>">
					<button class="ss-select-image button"><?php esc_html_e( 'Select image', 'slim-seo' ); ?></button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Recommended size: 800x418 px.', 'slim-seo' ); ?>
				</p>
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
		// @codingStandardsIgnoreLine.
		$data = isset( $_POST['slim_seo'] ) ? wp_unslash( $_POST['slim_seo'] ) : [];
		$data = $this->sanitize( $data );

		if ( empty( $data ) ) {
			delete_metadata( $this->object_type, $object_id, 'slim_seo' );
		} else {
			update_metadata( $this->object_type, $object_id, 'slim_seo', $data );
		}
	}

	private function sanitize( $data ) {
		$data = array_merge( $this->defaults, $data );

		$data['title']          = sanitize_text_field( $data['title'] );
		$data['description']    = sanitize_text_field( $data['description'] );
		$data['noindex']        = $data['noindex'] ? 1 : 0;
		$data['facebook_image'] = esc_url_raw( $data['facebook_image'] );
		$data['twitter_image']  = esc_url_raw( $data['twitter_image'] );

		return array_filter( $data );
	}

	private function get_data() {
		$data = get_metadata( $this->object_type, $this->get_object_id(), 'slim_seo', true );
		$data = $data ? $data : [];

		return array_merge( $this->defaults, $data );
	}

	abstract public function get_types();
	abstract protected function get_object_id();
}
