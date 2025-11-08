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
		if ( ! Settings::get( 'enable_edit_robots' ) ) {
			return $content;
		}

		$custom_robots = Settings::get( 'custom_robots' );

		return $custom_robots ?: $content;
	}
}
