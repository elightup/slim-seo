<?php
namespace SlimSEO\Settings\MetaTags;

class Item {
	private $option_key = '';
	private $defaults   = [
		'title'          => '',
		'description'    => '',
		'facebook_image' => '',
		'twitter_image'  => '',
	];

	public function __construct( string $option_key ) {
		$this->option_key = $option_key;
	}

	public function render(): void {
		$data = $this->get_data();
		?>
		<input type="hidden" id="ss-title-preview-<?= esc_attr( $this->option_key ) ?>" value="<?= esc_attr( $this->get_default_title() ) ?>">
		<input type="hidden" id="ss-description-preview-<?= esc_attr( $this->option_key ) ?>" value="<?= esc_attr( $this->get_default_description() ) ?>">
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
		<?php
	}

	private function get_default_title(): string {
		$parts = apply_filters( 'slim_seo_title_parts', [ 'site', 'tagline' ], 'post' );
		$title = '';

		if ( $this->option_key !== 'home' ) {
			$post_type = str_replace( '_archive', '', $this->option_key );
			$parts     = apply_filters( 'slim_seo_title_parts', [ 'title', 'site' ], 'post' );
			$title     = get_post_type_object( $post_type )->labels->name;
		}

		$values = [
			'site'    => get_bloginfo( 'name' ),
			'tagline' => get_bloginfo( 'description' ),
			'title'   => $title,
		];

		$parts = array_map( function ( string $part ) use ( $values ): string {
			return $values[ $part ] ?? '';
		}, $parts );

		$separator = apply_filters( 'document_title_separator', '-' ); // phpcs:ignore
		return implode( " $separator ", $parts );
	}

	private function get_default_description(): string {
		return $this->option_key === 'home' ? get_bloginfo( 'description' ) : '';
	}

	public function sanitize( array $data ): array {
		$data = array_merge( $this->defaults, $data );

		$data['title']          = sanitize_text_field( $data['title'] );
		$data['description']    = sanitize_text_field( $data['description'] );
		$data['facebook_image'] = esc_url_raw( $data['facebook_image'] );
		$data['twitter_image']  = esc_url_raw( $data['twitter_image'] );

		return array_filter( $data );
	}

	private function get_data(): array {
		$option = get_option( 'slim_seo' );
		$data   = $option[ $this->option_key ] ?? [];

		return array_merge( $this->defaults, $data );
	}
}
