<?php
namespace SlimSEO;

class Deactivator {
	public function __construct( $file ) {
		register_deactivation_hook( $file, [ $this, 'deactivate' ] );
	}

	public function deactivate( $network_wide ) {
		if ( is_multisite() && $network_wide ) {
			$this->network_deactivate();
		} else {
			$this->site_deactivate();
		}
	}

	private function network_deactivate() {
		$sites = get_sites(
			[
				'fields'            => 'ids',
				'number'            => 0,
				'update_site_cache' => false,
			]
		);
		foreach ( $sites as $site ) {
			switch_to_blog( $site );
			$this->site_deactivate();
			restore_current_blog();
		}
	}

	private function site_deactivate() {
		/*
		 * When deactivating the plugin, we hardly remove our rewrite rules.
		 * flush_rewrite_rules() not working as it re-adds our rewrite rules.
		 * So, let WordPress regenerates rewrite rules when needed.
		 */
		delete_option( 'rewrite_rules' );

		do_action( 'slim_seo_deactivate' );
	}
}
