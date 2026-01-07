<?php
namespace SlimSEO\Integrations;

use WP_Post;

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
		add_filter( 'slim_seo_post_content', [ $this, 'filter_content' ], 10, 2 );
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

	public function filter_content( string $post_content, WP_Post $post ): string {
		$content = $this->get_kadence_content( $post );

		return $content ?: $post_content;
	}

	private function get_kadence_content( WP_Post $post ): ?string {
		if ( empty( $post->post_content ) || ! has_blocks( $post->post_content ) ) {
			return null;
		}

		$blocks = parse_blocks( $post->post_content );

		if ( empty( $blocks ) ) {
			return null;
		}

		return $this->get_blocks_content( $blocks );
	}

	private function get_blocks_content( array $blocks ): string {
		$content = [];

		foreach ( $blocks as $block ) {
			$content[] = $this->get_block_content( $block );
		}

		return trim( implode( ' ', $content ) );
	}

	private function get_block_content( array $block ): string {
		$text = '';

		// Handle Inner blocks (nested) like kadence/rowlayout, core/group, core/columns.
		if ( ! empty( $block['innerBlocks'] ) ) {
			$text .= ' ' . $this->get_blocks_content( $block['innerBlocks'] );
		}

		if ( ! empty( $block['innerHTML'] ) ) {
			$text .= ' ' . wp_strip_all_tags( $block['innerHTML'] );
		}

		return trim( $text );
	}

}
