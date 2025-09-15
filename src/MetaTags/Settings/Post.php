<?php
namespace SlimSEO\MetaTags\Settings;

use SlimSEO\Helpers\Data;

class Post extends Base {
	public function setup(): void {
		$this->object_type = 'post';
		add_action( 'admin_print_styles-post.php', [ $this, 'enqueue' ] );
		add_action( 'admin_print_styles-post-new.php', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_meta_box_tabs', [ $this, 'tabs' ], 10 );
		add_filter( 'slim_seo_meta_box_panels', [ $this, 'panels' ], 10 );
		add_action( 'slim_seo_meta_box_content', [ $this, 'content' ], 10 );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function enqueue(): void {
		if ( $this->is_valid() ) {
			parent::enqueue();
		}
	}

	public function tabs( array $tabs ): array {
		if ( ! $this->is_valid() ) {
			return $tabs;
		}

		$tabs['general'] = esc_html__( 'General', 'slim-seo' );

		return $tabs;
	}

	public function content(): void {
		if ( ! $this->is_valid() ) {
			return;
		}

		wp_nonce_field( 'save', 'ss_nonce' );
		?>

		<div id="ss-single"></div>

		<?php
	}

	public function panels( array $panels ): array {
		if ( ! $this->is_valid() ) {
			return $panels;
		}

		ob_start();
		?>

		<div id="general" class="ss-tab-pane">
			<?php $this->content(); ?>
		</div>

		<?php
		$panels['general'] = ob_get_clean();

		return $panels;
	}

	public function is_valid(): bool {
		$post_types = $this->get_types();
		$screen     = get_current_screen();

		return in_array( $screen->post_type, $post_types, true );
	}

	public function get_types() {
		return Data::get_meta_box_post_types();
	}

	protected function get_object_id() {
		return get_the_ID();
	}
}
