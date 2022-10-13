<?php
namespace SlimSEO\Redirection\Database;

class Log404 {
	public function __construct() {
		global $wpdb;

		$wpdb->tables[]     = 'slim_seo_404';
		$wpdb->slim_seo_404 = $wpdb->prefix . 'slim_seo_404';

		$this->create();
	}

	public function create() {
		$option_name = 'ss_redirection_db_version';
		$db_version  = get_option( $option_name );

		if ( $db_version >= SLIM_SEO_VER ) {
			return;
		}

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$sql_query       = "
			CREATE TABLE IF NOT EXISTS {$wpdb->slim_seo_404} (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`url` varchar(255) NOT NULL,
				`hit` bigint(20) unsigned DEFAULT '0',
				`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
				`updated_at` datetime DEFAULT CURRENT_TIMESTAMP,

				PRIMARY KEY (`id`),

				KEY `url` (`url`)
			) $charset_collate;
		";

		dbDelta( $sql_query );

		update_option( $option_name, SLIM_SEO_VER );
	}

	public function get_log_by_url( string $value ) : array {
		global $wpdb;

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT *
				FROM {$wpdb->slim_seo_404}
				WHERE `url` = %s",
				$value
			),
			ARRAY_A
		);

		return ! empty( $row ) ? $row : [];
	}

	public function get_total() : int {
		global $wpdb;

		return $wpdb->get_var(
			"SELECT COUNT(*)
			FROM {$wpdb->slim_seo_404}"
		);
	}

	public function get_list( string $order_by = 'updated_at', string $order = 'DESC', int $limit = 0, int $offset = 0 ) : array {
		global $wpdb;

		$sql_query = "
			SELECT * 
			FROM {$wpdb->slim_seo_404} 
			ORDER BY `{$order_by}` {$order}
		";

		if ( $limit ) {
			$sql_query .= " LIMIT {$limit} OFFSET {$offset}";
		}

		return $wpdb->get_results(
			$sql_query,
			ARRAY_A
		);
	}

	public function add( array $log ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->slim_seo_404,
			$log
		);
	}

	public function update( array $log ) {
		global $wpdb;

		$wpdb->update(
			$wpdb->slim_seo_404,
			$log,
			[ 'id' => $log['id'] ]
		);
	}
}
