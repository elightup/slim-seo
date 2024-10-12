<?php
namespace SlimSEO\Integrations;

class Forminator {
	public function setup(): void {
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
		add_filter( 'slim_seo_skipped_blocks', [ $this, 'skip_blocks' ] );
	}

	public function skip_shortcodes( array $shortcodes ): array {
		return array_merge( $shortcodes, [
			'forminator_form',
			'forminator_poll',
			'forminator_quiz',
		] );
	}

	public function skip_blocks( array $blocks ): array {
		return array_merge( $blocks, [
			'forminator/forms',
			'forminator/polls',
			'forminator/quizzes',
		] );
	}
}
