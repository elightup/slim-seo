<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || die;

// phpcs:ignore
$delete_data = defined( 'SLIM_SEO_DELETE_DATA' ) ? SLIM_SEO_DELETE_DATA : false;
if ( ! $delete_data ) {
	return;
}

delete_option( 'slim_seo' );
delete_option( 'slim_seo_db_version' );
delete_option( 'ss_redirects' );
delete_option( 'ss_redirection_db_version' );

global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}slim_seo_404" );
