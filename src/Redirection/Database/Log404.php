<?php
namespace SlimSEO\Redirection\Database;

use SlimSEO\Redirection\Settings;

class Log404 {
	protected $option_name = 'ss_redirection_db_version';

	public function __construct() {
		global $wpdb;

		$wpdb->tables[]     = 'slim_seo_404';
		$wpdb->slim_seo_404 = $wpdb->prefix . 'slim_seo_404';
	}

	public function create_table(): void {
		if ( ! Settings::get( 'enable_404_logs' ) ) {
			return;
		}

		$db_version = get_option( $this->option_name );

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

		update_option( $this->option_name, SLIM_SEO_VER );
	}

	public function table_exists(): bool {
		return get_option( $this->option_name ) ? true : false;
	}

	public function drop_table(): void {
		global $wpdb;

		delete_option( $this->option_name );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->slim_seo_404}" );
	}

	public function get_log_by_url( string $value ): array {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$wpdb->slim_seo_404} WHERE `url` = %s", $value ),
			ARRAY_A
		);

		return ! empty( $row ) ? $row : [];
	}

	public function get_total(): int {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->slim_seo_404}" );
	}

	public function list( string $order_by = 'updated_at', string $order = 'DESC', int $limit = 0, int $offset = 0 ): array {
		global $wpdb;

		$sql_query = "SELECT * FROM {$wpdb->slim_seo_404} ORDER BY `{$order_by}` {$order}";

		if ( $limit ) {
			$sql_query .= " LIMIT {$limit} OFFSET {$offset}";
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results( $sql_query, ARRAY_A );
	}

	public function add( array $log ): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert( $wpdb->slim_seo_404, $log );
	}

	public function update( array $log ): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $wpdb->slim_seo_404, $log, [ 'id' => $log['id'] ] );
	}

	public function delete_older_logs( int $days ): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( "DELETE FROM {$wpdb->slim_seo_404} WHERE updated_at < NOW() - INTERVAL {$days} DAY" );
	}

	public function delete( int $id ): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->slim_seo_404} WHERE `id` = %d", $id ) );
	}

	public function delete_all(): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( "DELETE FROM {$wpdb->slim_seo_404}" );
	}
}
