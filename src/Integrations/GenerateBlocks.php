<?php
namespace SlimSEO\Integrations;

class GenerateBlocks {
	public function is_active(): bool {
		return defined( 'GENERATEBLOCKS_VERSION' );
	}

	public function setup(): void {
		add_filter( 'slim_seo_allowed_blocks', [ $this, 'allowed_blocks' ] );
	}

	public function allowed_blocks( array $blocks ): array {
		$blocks[] = 'generateblocks/element';
		$blocks[] = 'generateblocks/text';
		return $blocks;
	}
}