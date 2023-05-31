<?php
namespace SlimSEO\Helpers;

class UI {
	public static function tooltip( string $content, string $icon = '<span class="dashicons dashicons-editor-help"></span>', string $placement = 'right' ) {
		static $output_script = false;

		echo '<span class="ss-tooltip" data-tippy-content="', esc_attr( $content ), '">', wp_kses_post( $icon ), '</span>';

		if ( $output_script === true ) {
			return;
		}

		wp_enqueue_script( 'tippy', 'https://cdn.jsdelivr.net/combine/npm/@popperjs/core@2.11.2/dist/umd/popper.min.js,npm/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js', [], '6.3.7', true );
		wp_add_inline_script( 'tippy', "tippy( '.ss-tooltip', {
			placement: '$placement',
			arrow: true,
			animation: 'fade'
		} );" );
		$output_script = true;
	}

	public static function toggle( string $name, string $value, bool $checked, string $title = '' ) {
		?>
		<label class="ss-toggle">
			<input type="checkbox" name="<?= esc_attr( $name ) ?>" value="<?= esc_attr( $value ) ?>"<?php checked( $checked ) ?>>
			<div class="ss-toggle__switch"></div>
			<?php if ( $title ) : ?>
				<span class="ss-toggle__label"><?= esc_html( $title ) ?></span>
			<?php endif ?>
		</label>
		<?php
	}
}
