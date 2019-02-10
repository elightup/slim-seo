<?php
namespace SlimSEO;

class Activator {
	public function __construct( $file ) {
		register_activation_hook( $file, [ $this, 'activate' ] );
	}

	public function activate( $network_wide ) {
		if ( is_multisite() && $network_wide ) {
			$this->network_activate();
		} else {
			$this->site_activate();
		}
	}

	private function network_activate() {
		$sites = get_sites(
			[
				'fields'            => 'ids',
				'number'            => 0,
				'update_site_cache' => false,
			]
		);
		foreach ( $sites as $site ) {
			switch_to_blog( $site );
			$this->site_activate();
			restore_current_blog();
		}
	}

	private function site_activate() {
		// Update rewrite rules. @see Deactivator class.
		delete_option( 'rewrite_rules' );
	}
}
