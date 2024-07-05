<?php
namespace SlimSEO\MetaTags\Settings;

use SlimSEO\Helpers\UI;

abstract class Base {
	protected $object_type;
	protected $title;
	protected $defaults = [
		'title'          => '',
		'description'    => '',
		'facebook_image' => '',
		'twitter_image'  => '',
		'canonical'      => '',
		'noindex'        => 0,
	];

	public function enqueue() {
		wp_enqueue_media();
		wp_enqueue_style( 'slim-seo-meta-tags', SLIM_SEO_URL . 'css/meta-tags.css', [], SLIM_SEO_VER );
		wp_enqueue_script( 'slim-seo-meta-tags', SLIM_SEO_URL . 'js/meta-tags/dist/object.js', [ 'jquery', 'underscore' ], SLIM_SEO_VER, true );
		wp_localize_script( 'slim-seo-meta-tags', 'ss', $this->get_script_params() );

		wp_enqueue_style( 'slim-seo-post-types', SLIM_SEO_URL . 'css/post-types.css', [], filemtime( SLIM_SEO_DIR . 'css/post-types.css' ) );
		wp_enqueue_script( 'slim-seo-post-type', SLIM_SEO_URL . 'js/post-type.js', [ 'jquery', 'underscore', 'wp-element', 'wp-components', 'wp-i18n' ], filemtime( SLIM_SEO_DIR . 'js/post-type.js' ), true );
		wp_localize_script( 'slim-seo-post-type', 'ssPostType', [
			'title'           => $this->title,
			'metadata'        => get_metadata( $this->object_type, get_the_ID(), 'slim_seo', true ),
			'mediaPopupTitle' => __( 'Select An Image', 'slim-seo-schema' ),
		] );
	}

	protected function get_script_params(): array {
		$params = [
			'mediaPopupTitle' => __( 'Select An Image', 'slim-seo' ),
			'site'            => [
				'title'       => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ),
				'description' => html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES, 'UTF-8' ),
			],
			'title'           => [
				'separator' => apply_filters( 'document_title_separator', '-' ), // phpcs:ignore
				'parts'     => apply_filters( 'slim_seo_title_parts', [ 'title', 'site' ], $this->object_type ),
			],
		];
		return $params;
	}

	public function render() {
		wp_nonce_field( 'save', 'ss_nonce' );
		?>

		<?php if ( $this->title ) : ?>
			<h2><?= esc_html( $this->title ); ?></h2>
		<?php endif; ?>
			<div id="ss-post-type"></div>
		<?php
	}

	public function save( $object_id ) {
		if ( ! check_ajax_referer( 'save', 'ss_nonce', false ) || empty( $_POST ) ) {
			return;
		}

		$data = isset( $_POST['slim_seo'] ) ? wp_unslash( $_POST['slim_seo'] ) : []; // phpcs:ignore

		// Do not erase existing data when quick editing.
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'inline-save' ) { // phpcs:ignore
			$existing_data = get_metadata( $this->object_type, $object_id, 'slim_seo', true ) ?: [];
			$data          = array_merge( $existing_data, $data );
		}

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
		$data['facebook_image'] = esc_url_raw( $data['facebook_image'] );
		$data['twitter_image']  = esc_url_raw( $data['twitter_image'] );
		$data['canonical']      = esc_url_raw( $data['canonical'] );
		$data['noindex']        = $data['noindex'] ? 1 : 0;

		return array_filter( $data );
	}

	private function get_data() {
		$data = get_metadata( $this->object_type, $this->get_object_id(), 'slim_seo', true );
		$data = is_array( $data ) && ! empty( $data ) ? $data : [];

		return array_merge( $this->defaults, $data );
	}

	abstract public function get_types();
	abstract protected function get_object_id();
}
