<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || die;

delete_option( 'slim_seo' );
delete_option( 'slim_seo_db_version' );
delete_option( 'ss_redirects' );
delete_option( 'ss_redirection_db_version' );

global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}slim_seo_404" );
