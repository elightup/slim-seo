<?php
namespace SlimSEO\Migration\Sources;

use SlimSEO\Redirection\Database\Redirects as DbRedirects;
use SlimSEO\Redirection\Helper as RedirectionHelper;

class Redirects301 extends Source {
	protected $constant = 'EPS_REDIRECT_VERSION';

	public function migrate_redirects() {
		$count = 0;

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}redirects", ARRAY_A );

		if ( empty( $results ) ) {
			return $count;
		}

		$db_redirects   = new DbRedirects();
		$redirect_types = RedirectionHelper::redirect_types();

		foreach ( $results as $result ) {
			// Ignore if From URL exists
			if ( $db_redirects->exists( $result['url_from'] ) ) {
				continue;
			}

			$status   = $result['status'];
			$to       = $result['url_to'];
			$redirect = [
				'type'             => isset( $redirect_types[ $status ] ) ? $status : 301,
				'condition'        => 'exact-match',
				'from'             => $result['url_from'],
				'to'               => 'post' === $result['type'] ? get_permalink( $to ) : $to,
				'note'             => '',
				'enable'           => 'inactive' === $status ? 0 : 1,
				'ignoreParameters' => 0,
			];

			$db_redirects->update( $redirect );

			++$count;
		}

		return $count;
	}
}
