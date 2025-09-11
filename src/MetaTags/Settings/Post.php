<?php
namespace SlimSEO\MetaTags\Settings;

use SlimSEO\Helpers\Data;

class Post extends Base {
	public function setup(): void {
		$this->object_type = 'post';
		add_action( 'admin_print_styles-post.php', [ $this, 'enqueue' ] );
		add_action( 'admin_print_styles-post-new.php', [ $this, 'enqueue' ] );
		add_action( 'slim_seo_metabox_tabs', [ $this, 'tabs' ], 10 );
		add_action( 'slim_seo_metabox_panels', [ $this, 'panels' ], 10 );
		add_action( 'slim_seo_metabox_content', [ $this, 'content' ], 10 );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function enqueue(): void {
		$post_types = $this->get_types();
		$screen     = get_current_screen();

		if ( in_array( $screen->post_type, $post_types, true ) ) {
			parent::enqueue();
		}
	}

	public function tabs( $tabs ) {
		$tabs['general'] = esc_html__( 'General', 'slim-seo' );

		return $tabs;
	}

	public function content() {
		wp_nonce_field( 'save', 'ss_nonce' );
		?>

		<div id="ss-single"></div>

		<?php
	}

	public function panels( $panels ) {
		ob_start();
		?>

		<div id="general" class="ss-tab-pane">
			<?php $this->content(); ?>
		</div>

		<?php
		$panels['general'] = ob_get_clean();

		return $panels;
	}

	public function get_types() {
		return Data::get_metabox_post_types();
	}

	protected function get_object_id() {
		return get_the_ID();
	}
}
