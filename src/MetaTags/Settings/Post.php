<?php
namespace SlimSEO\MetaTags\Settings;

use SlimSEO\Helpers\Data;
use SlimSEO\Helpers\Option;

class Post extends Base {
	public function setup(): void {
		$this->object_type = 'post';

		add_action( 'slim_seo_meta_box_enqueue', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_meta_box_tabs', [ $this, 'tabs' ], 10 );
		add_filter( 'slim_seo_meta_box_panels', [ $this, 'panels' ], 10 );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function tabs( array $tabs ): array {
		if ( $this->hide_from_search_results() ) {
			return $tabs;
		}

		$tabs['general'] = esc_html__( 'General', 'slim-seo' );

		return $tabs;
	}

	public function panels( array $panels ): array {
		if ( $this->hide_from_search_results() ) {
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
		return Data::get_meta_box_post_types();
	}

	protected function get_object_id(): int {
		return (int) get_the_ID();
	}

	protected function hide_from_search_results(): bool {
		$post_type = get_post_type( $this->get_object_id() );

		return $post_type ? (bool) Option::get( "{$post_type}.noindex", false ) : false;
	}
}
