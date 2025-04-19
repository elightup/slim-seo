<?php
/**
 * Plugin Name:       Slim SEO
 * Plugin URI:        https://wpslimseo.com
 * Description:       A fast and automated SEO plugin for WordPress.
 * Author:            Slim SEO
 * Author URI:        https://wpslimseo.com
 * Version:           4.5.2
 * License:           GPL v3
 * GitHub Plugin URI: elightup/slim-seo
 *
 * Copyright (C) 2010-2025 Tran Ngoc Tuan Anh. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace SlimSEO;

defined( 'ABSPATH' ) || die;

define( 'SLIM_SEO_DIR', plugin_dir_path( __FILE__ ) );
define( 'SLIM_SEO_URL', plugin_dir_url( __FILE__ ) );
define( 'SLIM_SEO_REDIRECTS', 'ss_redirects' );
define( 'SLIM_SEO_DELETE_404_LOGS_ACTION', 'delete_404_logs' );
define( 'SLIM_SEO_VER', '4.5.2' );
define( 'SLIM_SEO_DB_VER', 1 );

require __DIR__ . '/vendor/autoload.php';

new Activator( __FILE__ );
new Deactivator( __FILE__ );

$slim_seo = new Container();
$slim_seo->register_services();

// Initialize at priority 5 to be able to disable core sitemaps completely which runs at priority 10.
add_action( 'init', [ $slim_seo, 'init' ], 5 );
