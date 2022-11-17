<?php
namespace SlimSEO\Redirection\Migration;

class Redirects301 extends Replacer {
	public function migrate() : int {
		$migrated_redirects = 0;

		global $wpdb;

		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}redirects", ARRAY_A );

		if ( empty( $results ) ) {
			return $migrated_redirects;
		}

		foreach ( $results as $result ) {
			// Ignore if From URL exists
			if ( $this->db_redirects->exists( $result['url_from'] ) ) {
				continue;
			}

			$status   = $result['status'];
			$to       = $result['url_to'];
			$redirect = [
				'type'             => isset( $this->redirect_types[ $status ] ) ? $status : 301,
				'condition'        => 'exact-match',
				'from'             => $result['url_from'],
				'to'               => 'post' === $result['type'] ? get_permalink( $to ) : $to,
				'note'             => '',
				'enable'           => 'inactive' === $status ? 0 : 1,
				'ignoreParameters' => 0,
			];

			$this->db_redirects->update( $redirect );

			$migrated_redirects++;
		}

		return $migrated_redirects;
	}

	public function is_activated() : bool {
		return defined( 'EPS_REDIRECT_VERSION' );
	}
}
