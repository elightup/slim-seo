<?php
namespace SlimSEO\Settings;

class Post {
	public static function setup(): void {
		add_action( 'admin_print_styles-post.php', [ __CLASS__, 'enqueue' ] );
		add_action( 'admin_print_styles-post-new.php', [ __CLASS__, 'enqueue' ] );
		add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_box' ] );
	}

	public static function enqueue(): void {
		$tabs = apply_filters( 'slim_seo_meta_box_tabs', [] );

		if ( count( $tabs ) > 1 ) {
			wp_enqueue_script( 'slim-seo-components', 'https://cdn.jsdelivr.net/gh/elightup/slim-seo@master/js/components.js', [], '1.0.0', true );
		}
	}

	public static function add_meta_box() {
		$tabs = apply_filters( 'slim_seo_meta_box_tabs', [] );

		if ( empty( $tabs ) ) {
			return;
		}

		$context    = apply_filters( 'slim_seo_meta_box_context', 'normal' );
		$priority   = apply_filters( 'slim_seo_meta_box_priority', 'low' );
		$post_types = self::get_post_types();

		foreach ( $post_types as $post_type ) {
			add_meta_box( 'slim-seo', apply_filters( 'slim_seo_meta_box_title', __( 'Search Engine Optimization', 'slim-seo' ) ), [ __CLASS__, 'render' ], $post_type, $context, $priority );
		}
	}

	public static function render(): void {
		$tabs = apply_filters( 'slim_seo_meta_box_tabs', [] );

		if ( empty( $tabs ) ) {
			return;
		}

		if ( 1 === count( $tabs ) ) {
			do_action( 'slim_seo_meta_box_content' );

			return;
		}
		?>

		<nav class="ss-tab-list">
			<?php
			foreach ( $tabs as $key => $label ) {
				printf( '<a href="#%s" class="ss-tab">%s</a>', esc_attr( $key ), esc_html( $label ) );
			}
			?>
		</nav>

		<?php
		$panels = apply_filters( 'slim_seo_meta_box_panels', [] );

		echo implode( '', $panels ); // phpcs:ignore
	}

	public static function get_post_types(): array {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );

		unset( $post_types['attachment'] );

		$post_types = apply_filters( 'slim_seo_post_types', $post_types );
		$post_types = array_keys( $post_types );
		$post_types = apply_filters( 'slim_seo_meta_box_post_types', $post_types );

		return $post_types;
	}
}
