<?php
namespace SlimSEO\MetaTags\Settings;

use SlimSEO\Helpers\Data;

class Post extends Base {
	public function setup(): void {
		$this->object_type = 'post';

		add_action( 'slim_seo_meta_box_enqueue', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_meta_box_tabs', [ $this, 'tabs' ], 10 );
		add_filter( 'slim_seo_meta_box_panels', [ $this, 'panels' ], 10 );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function tabs( array $tabs ): array {
		if ( ! $this->is_screen() ) {
			return $tabs;
		}

		$tabs['general'] = esc_html__( 'General', 'slim-seo' );

		return $tabs;
	}

	public function panels( array $panels ): array {
		if ( ! $this->is_screen() ) {
			return $panels;
		}

		ob_start();

		wp_nonce_field( 'save', 'ss_nonce' );
		?>

		<div id="ss-single"></div>

		<?php
		$panels['general'] = ob_get_clean();

		return $panels;
	}

	public function get_types(): array {
		$option     = get_option( 'slim_seo', [] );
		$post_types = Data::get_meta_box_post_types();
		$post_types = array_filter( $post_types, function ( $post_type ) use ( $option ) {
			return empty( $option[ $post_type ]['noindex'] );
		} );

		return $post_types;
	}

	protected function get_object_id(): int {
		return (int) get_the_ID();
	}

	protected function is_screen(): bool {
		$screen = get_current_screen();

		return in_array( $screen->post_type, $this->get_types(), true );
	}
}
