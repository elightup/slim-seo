<?php
namespace SlimSEO\MetaTags\Settings;

use SlimSEO\Helpers\Assets;

abstract class Base {
	protected $object_type;
	protected $defaults = [
		'title'          => '',
		'description'    => '',
		'facebook_image' => '',
		'twitter_image'  => '',
		'canonical'      => '',
		'noindex'        => 0,
	];

	public function enqueue(): void {
		wp_enqueue_media();

		wp_enqueue_style( 'slim-seo-meta-tags', SLIM_SEO_URL . 'css/meta-tags.css', [ 'wp-components' ], filemtime( SLIM_SEO_DIR . 'css/meta-tags.css' ) );
		Assets::enqueue_build_js( 'single', 'ss', [
			'mediaPopupTitle' => __( 'Select An Image', 'slim-seo' ),
			'id'              => $this->get_object_id(),
			'data'            => $this->get_data(),
		] );
	}

	public function render(): void {
		$tabs = apply_filters( 'slim_seo_metabox_tabs', [] );

		if ( empty( $tabs ) ) {
			$this->general_tab();
			return;
		}
		?>

		<nav class="ss-tab-list">
			<a href="#general" class="ss-tab"><?php esc_html_e( 'General', 'slim-seo' ); ?></a>
			<?php
			foreach ( $tabs as $key => $label ) {
				printf( '<a href="#%s" class="ss-tab">%s</a>', esc_attr( $key ), esc_html( $label ) );
			}
			?>
		</nav>

		<div id="general" class="ss-tab-pane">
			<?php $this->general_tab(); ?>
		</div>

		<?php
		$panes = apply_filters( 'slim_seo_metabox_panels', [] );
		echo implode( '', $panes ); // phpcs:ignore
	}

	public function general_tab(): void {
		wp_nonce_field( 'save', 'ss_nonce' );
		?>
		<div id="ss-single"></div>
		<?php
	}

	public function save( $object_id ) {
		if ( ! check_ajax_referer( 'save', 'ss_nonce', false ) || empty( $_POST ) ) {
			return;
		}

		$data = isset( $_POST['slim_seo'] ) ? wp_unslash( $_POST['slim_seo'] ) : []; // phpcs:ignore
		$data = array_merge( $this->defaults, $data );

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
		$data['facebook_image'] = sanitize_text_field( $data['facebook_image'] );
		$data['twitter_image']  = sanitize_text_field( $data['twitter_image'] );
		$data['canonical']      = sanitize_text_field( $data['canonical'] );
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
