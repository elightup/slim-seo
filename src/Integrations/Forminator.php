<?php
namespace SlimSEO\Integrations;

class Forminator {
	public function setup(): void {
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
	}

	public function skip_shortcodes( array $shortcodes ): array {
		return array_merge( $shortcodes, [
			'forminator_form',
			'forminator_poll',
			'forminator_quiz',
		] );
	}
}
