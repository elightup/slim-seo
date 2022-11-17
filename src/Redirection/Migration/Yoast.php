<?php
namespace SlimSEO\Redirection\Migration;

class Yoast extends Replacer {
	public function migrate() : int {
		$migrated_redirects = 0;

		$results = get_option( 'wpseo-premium-redirects-base' ) ?: [];

		if ( empty( $results ) ) {
			return $migrated_redirects;
		}

		foreach ( $results as $result ) {
			// Ignore if From URL exists
			if ( $this->db_redirects->exists( $result['origin'] ) ) {
				continue;
			}

			$type     = $result['type'];
			$redirect = [
				'type'             => isset( $this->redirect_types[ $type ] ) ? $type : 301,
				'condition'        => 'regex' === $result['format'] ? 'regex' : 'exact-match',
				'from'             => $result['origin'],
				'to'               => $result['url'],
				'note'             => '',
				'enable'           => 1,
				'ignoreParameters' => 0,
			];

			$this->db_redirects->update( $redirect );

			$migrated_redirects++;
		}

		return $migrated_redirects;
	}

	public function is_activated() : bool {
		return defined( 'WPSEO_PREMIUM_VERSION' );
	}
}
