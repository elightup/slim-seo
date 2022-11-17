<?php
namespace SlimSEO\Redirection\Migration;

class Redirection extends Replacer {
	public function migrate() : int {
		$migrated_redirects = 0;

		global $wpdb;

		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}redirection_items", ARRAY_A );

		if ( empty( $results ) ) {
			return $migrated_redirects;
		}

		foreach ( $results as $result ) {
			// Ignore if From URL exists
			if ( $this->db_redirects->exists( $result['url'] ) ) {
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
				'type'             => isset( $this->redirect_types[ $action_code ] ) ? $action_code : 301,
				'condition'        => 'regex' === ( $result['match_url'] ?? '' ) ? 'regex' : 'exact-match',
				'from'             => $result['url'],
				'to'               => $result['action_data'] ?? '',
				'note'             => $result['title'] ?? '',
				'enable'           => 'enabled' === $result['status'] ? 1 : 0,
				'ignoreParameters' => $ignore_parameters,
			];

			$this->db_redirects->update( $redirect );

			$migrated_redirects++;
		}

		return $migrated_redirects;
	}

	public function is_activated() : bool {
		return defined( 'REDIRECTION_DB_VERSION' );
	}
}
