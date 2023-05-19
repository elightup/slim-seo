<?php
/**
 * Plugin Name: Slim SEO
 * Plugin URI:  https://wpslimseo.com
 * Description: A fast and automated SEO plugin for WordPress.
 * Author:      eLightUp
 * Author URI:  https://elightup.com
 * Version:     3.20.2
 * Text Domain: slim-seo
 * Domain Path: /languages
 */

namespace SlimSEO;

defined( 'ABSPATH' ) || die;

define( 'SLIM_SEO_DIR', plugin_dir_path( __FILE__ ) );
define( 'SLIM_SEO_URL', plugin_dir_url( __FILE__ ) );
define( 'SLIM_SEO_REDIRECTS', 'ss_redirects' );
define( 'SLIM_SEO_DELETE_404_LOGS_ACTION', 'delete_404_logs' );
define( 'SLIM_SEO_VER', '3.20.2' );

require __DIR__ . '/vendor/autoload.php';

new Activator( __FILE__ );
new Deactivator( __FILE__ );

$slim_seo = new Plugin;
$slim_seo->register_services();

// Initialize at priority 5 to be able to disable core sitemaps completely which runs at priority 10.
add_action( 'init', [ $slim_seo, 'init' ], 5 );
