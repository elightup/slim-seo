<?php
namespace SlimSEO\Robots;

class Loader {
	public function setup(): void {
		if ( is_admin() ) {
			new Settings();
		}

		add_filter( 'robots_txt', [ $this, 'robots_txt' ], 9999 );
	}

	public function robots_txt( string $content ): string {
		if ( ! Settings::get( 'robots_txt_editable' ) ) {
			return $content;
		}

		$robots_txt_content = Settings::get( 'robots_txt_content' );

		return $robots_txt_content ?: $content;
	}
}
