<?php
namespace SlimSEO\Integrations;

class Kadence {
	public function is_active(): bool {
		return defined( 'KADENCE_VERSION' )
			|| defined( 'KADENCE_BLOCKS_VERSION' )
			|| defined( 'KADENCE_CONVERSIONS_VERSION' )
			|| defined( 'KADENCE_INSIGHTS_VERSION' )
			|| defined( 'KT_CUSTOM_FONTS_VERSION' )
			|| defined( 'KBP_VERSION' )
			|| defined( 'KTP_VERSION' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
	}

	public function remove_post_types( array $post_types ): array {
		$unsupported = [
			'kadence_element',
			'kadence_form',
			'kadence_navigation',
			'kadence_header',
			'kadence_query',
			'kadence_query_card',
			'kadence_conversions',
			'kadence_ab_test',
			'kadence_lottie',
			'kadence_custom_svg',
			'kadence_adv_page',
			'kt_font',
			'kb_icon',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}

	public function skip_shortcodes( array $shortcodes ): array {
		return array_merge( $shortcodes, [
			'kb-dynamic',
			'kadence_breadcrumbs',
			'kadence_dark_mode',
			'kadence_element',
		] );
	}
}
