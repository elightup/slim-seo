<?php
namespace SlimSEO\Migration\Sources;

use SlimSEO\Redirection\Database\Redirects as DbRedirects;
use SlimSEO\Redirection\Helper as RedirectionHelper;

class Redirection extends Source {
	protected $constant = 'REDIRECTION_DB_VERSION';

	public function migrate_redirects() {
		$count = 0;

		global $wpdb;

		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}redirection_items", ARRAY_A );

		if ( empty( $results ) ) {
			return $count;
		}

		$db_redirects   = new DbRedirects();
		$redirect_types = RedirectionHelper::redirect_types();

		foreach ( $results as $result ) {
			// Ignore if From URL exists
			if ( $db_redirects->exists( $result['url'] ) ) {
				continue;
			}

			$action_code       = $result['action_code'];
			$ignore_parameters = 0;

			if ( ! empty( $result['match_data'] ) ) {
				$match_data = json_decode( $result['match_data'] );

				if ( 'ignore' === ( $match_data->source->flag_query ?? '' ) ) {
					$ignore_parameters = 1;
				}
			}

			$redirect = [
				'type'             => isset( $redirect_types[ $action_code ] ) ? $action_code : 301,
				'condition'        => 'regex' === ( $result['match_url'] ?? '' ) ? 'regex' : 'exact-match',
				'from'             => $result['url'],
				'to'               => $result['action_data'] ?? '',
				'note'             => $result['title'] ?? '',
				'enable'           => 'enabled' === $result['status'] ? 1 : 0,
				'ignoreParameters' => $ignore_parameters,
			];

			$db_redirects->update( $redirect );

			++$count;
		}

		return $count;
	}
}
