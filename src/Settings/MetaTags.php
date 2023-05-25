<?php
namespace SlimSEO\Settings;

class MetaTags {
	private $option_key = '';
	private $args       = [];
	private $defaults   = [
		'title'          => '',
		'description'    => '',
		'facebook_image' => '',
		'twitter_image'  => '',
		'canonical'      => '',
		'noindex'        => 0,
	];

	public function __construct( string $option_key, array $args = [] ) {
		$this->option_key = $option_key;
		$this->args       = $args;
	}

	public static function enqueue() {
		wp_enqueue_media();
		wp_enqueue_style( 'slim-seo-meta-tags', SLIM_SEO_URL . 'css/meta-tags.css', [], filemtime( SLIM_SEO_DIR . '/css/meta-tags.css' ) );
		wp_enqueue_script( 'slim-seo-meta-tags', SLIM_SEO_URL . 'js/meta-tags.js', [ 'jquery', 'underscore' ], filemtime( SLIM_SEO_DIR . '/js/meta-tags.js' ), true );
		wp_localize_script( 'slim-seo-meta-tags', 'ss', [
			'mediaPopupTitle' => __( 'Select An Image', 'slim-seo' ),
			'site'            => [
				'title'       => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ),
				'description' => html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES, 'UTF-8' ),
			],
			'title'           => [
				'separator' => apply_filters( 'document_title_separator', '-' ),
				'parts'     => apply_filters( 'slim_seo_title_parts', [ 'title', 'site' ], 'post' ),
			],
		] );
	}

	public function render() {
		$data = $this->get_data();
		?>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-title-<?= esc_attr( $this->option_key ) ?>"><?php esc_html_e( 'Meta title', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<input type="text" id="ss-title-<?= esc_attr( $this->option_key ) ?>" name="slim_seo[<?= esc_attr( $this->option_key ) ?>][title]" value="<?= esc_attr( $data['title'] ); ?>">
				<p class="description">
					<?php esc_html_e( 'Character count:', 'slim-seo' ); ?>
					<span class="ss-counter">0</span>.
					<?php esc_html_e( 'Recommended length: ≤ 60 characters.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-description-<?= esc_attr( $this->option_key ) ?>"><?php esc_html_e( 'Meta description', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<textarea id="ss-description-<?= esc_attr( $this->option_key ) ?>" name="slim_seo[<?= esc_attr( $this->option_key ) ?>][description]" rows="3"><?= esc_textarea( $data['description'] ); ?></textarea>
				<p class="description">
					<?php esc_html_e( 'Character count:', 'slim-seo' ); ?>
					<span class="ss-counter">0</span>.
					<?php esc_html_e( 'Recommended length: 50-160 characters.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-facebook-image-<?= esc_attr( $this->option_key ) ?>"><?php esc_html_e( 'Facebook image', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<div class="ss-input-group">
					<input type="text" id="ss-facebook-image-<?= esc_attr( $this->option_key ) ?>" name="slim_seo[<?= esc_attr( $this->option_key ) ?>][facebook_image]" value="<?= esc_attr( $data['facebook_image'] ); ?>">
					<button class="ss-select-image button"><?php esc_html_e( 'Select image', 'slim-seo' ); ?></button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Recommended size: 1200x630 px.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-twitter-image-<?= esc_attr( $this->option_key ) ?>"><?php esc_html_e( 'Twitter image', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<div class="ss-input-group">
					<input type="text" id="ss-twitter-image-<?= esc_attr( $this->option_key ) ?>" name="slim_seo[<?= esc_attr( $this->option_key ) ?>][twitter_image]" value="<?= esc_attr( $data['twitter_image'] ); ?>">
					<button class="ss-select-image button"><?php esc_html_e( 'Select image', 'slim-seo' ); ?></button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
		<div class="ss-field ss-field-checkbox">
			<div class="ss-label">
				<label for="ss-noindex-<?= esc_attr( $this->option_key ) ?>"><?php esc_html_e( 'Hide from search results', 'slim-seo' ); ?></label>
			</div>
			<div class="ss-input">
				<input type="checkbox" id="ss-noindex-<?= esc_attr( $this->option_key ) ?>" name="slim_seo[<?= esc_attr( $this->option_key ) ?>][noindex]" value="1" <?php checked( $data['noindex'] ); ?>>
			</div>
		</div>
		<?php
	}

	public function sanitize( array $data ) {
		$data = array_merge( $this->defaults, $data );

		$data['title']          = sanitize_text_field( $data['title'] );
		$data['description']    = sanitize_text_field( $data['description'] );
		$data['facebook_image'] = esc_url_raw( $data['facebook_image'] );
		$data['twitter_image']  = esc_url_raw( $data['twitter_image'] );
		$data['canonical']      = esc_url_raw( $data['canonical'] );
		$data['noindex']        = $data['noindex'] ? 1 : 0;

		return array_filter( $data );
	}

	private function get_data() {
		$data = get_option( 'slim_seo', [] ) ?: [];
		$data = is_array( $data ) && ! empty( $data ) ? $data : [];
		$data = $data[ $this->option_key ] ?? [];

		return array_merge( $this->defaults, $data );
	}
}
