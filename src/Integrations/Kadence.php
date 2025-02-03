<?php
namespace SlimSEO\Integrations;

class Kadence {
	public function is_active(): bool {
		return defined( 'KADENCE_VERSION' )
			|| defined( 'KADENCE_BLOCKS_VERSION' )
			|| defined( 'KADENCE_CONVERSIONS_VERSION' )
			|| defined( 'KADENCE_INSIGHTS_VERSION' )
			|| defined( 'KT_CUSTOM_FONTS_VERSION' )
			|| defined( 'KTP_VERSION' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_post_types', [ $this, 'remove_post_types' ] );
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
			'kt_font',
			'kt_icon',
		];
		return array_diff_key( $post_types, array_flip( $unsupported ) );
	}
}
