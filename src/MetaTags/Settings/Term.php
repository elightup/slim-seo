<?php
namespace SlimSEO\MetaTags\Settings;

class Term extends Base {
	public function setup(): void {
		$this->object_type = 'term';

		// Priority 99 makes sure all taxonomies are registered.
		add_action( 'init', [ $this, 'register_hooks' ], 99 );
	}

	public function render(): void {
		wp_nonce_field( 'save', 'ss_nonce' );
		?>
		<div id="ss-single"></div>
		<?php
	}

	public function register_hooks(): void {
		add_action( 'admin_print_styles-term.php', [ $this, 'enqueue' ] );

		$taxonomies = $this->get_types();

		foreach ( $taxonomies as $taxonomy ) {
			add_action( "{$taxonomy}_edit_form", [ $this, 'render' ] );
			add_action( "edited_$taxonomy", [ $this, 'save' ] );
		}
	}

	public function get_types(): array {
		$option     = get_option( 'slim_seo', [] );
		$taxonomies = get_taxonomies( [ 'public' => true ] );
		$taxonomies = apply_filters( 'slim_seo_meta_box_taxonomies', $taxonomies );
		$taxonomies = array_filter( $taxonomies, function ( $taxonomy ) use ( $option ) {
			return empty( $option[ $taxonomy ]['noindex'] );
		} );

		return $taxonomies;
	}

	protected function get_object_id(): int {
		return (int) filter_input( INPUT_GET, 'tag_ID', FILTER_SANITIZE_NUMBER_INT );
	}

	protected function is_screen(): bool {
		$screen = get_current_screen();

		return in_array( $screen->taxonomy, $this->get_types(), true );
	}
}
