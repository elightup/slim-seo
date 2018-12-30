<?php
/**
 * Plugin Name: Slim SEO
 * Plugin URI:  https://elightup.com/wordpress-plugins/slim-seo/
 * Description: A lightweight SEO plugin for WordPress.
 * Author:      eLightUp
 * Author URI:  https://elightup.com
 * Version:     1.0.0
 * Text Domain: slim-seo
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || die;

define( 'SLIM_SEO_DIR', plugin_dir_path( __FILE__ ) );
define( 'SLIM_SEO_URL', plugin_dir_url( __FILE__ ) );

require __DIR__ . '/vendor/autoload.php';

$title = new SlimSEO\MetaTags\Title();
$description = new SlimSEO\MetaTags\Description();
new SlimSEO\MetaTags\OpenGraph( $title, $description );
new SlimSEO\MetaTags\Twitter();
new SlimSEO\Sitemaps\Router();
