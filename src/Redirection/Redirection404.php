<?php
namespace SlimSEO\Redirection;

use SlimSEO\Redirection\Database\Log404 as DbLog;

class Redirection404 {
	protected $db_log;

	public function __construct( DbLog $db_log ) {
		add_action( 'template_redirect', [ $this, 'redirect' ], 20 );
		
		if ( ! Settings::get( 'enable_404_logs' ) ) {
			return;
		}

		$this->db_log = $db_log;

		add_action( 'template_redirect', [ $this, 'log' ], 15 );
	}

	public function log() {
		if ( ! is_404() ) {
			return;
		}

		$http_host   = $_SERVER['HTTP_HOST'] ?? ''; // @codingStandardsIgnoreLine.
		$request_uri = $_SERVER['REQUEST_URI'] ?? ''; // @codingStandardsIgnoreLine.
		$request_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$http_host}{$request_uri}";
		$request_url = Helper::normalize_url( $request_url );
		$log         = $this->db_log->get_log_by_url( $request_url );
		$now         = current_datetime()->format( 'Y-m-d H:i:s' );

		if ( empty( $log ) ) {
			$this->db_log->add( [
				'url'        => $request_url,
				'hit'        => 1,
				'created_at' => $now,
				'updated_at' => $now,
			] );
		} else {
			$log['hit']        = (int) $log['hit'] + 1;
			$log['updated_at'] = $now;

			$this->db_log->update( $log );
		}
	}

	public function redirect() {
		if ( ! is_404() ) {
			return;
		}

		$redirect_to = Settings::get( 'redirect_404_to' );

		if ( ! $redirect_to ) {
			return;
		}

		switch ( $redirect_to ) {
			case 'custom':
				$to = Settings::get( 'redirect_404_to_url' );
				break;

			default:
				$to = home_url();
				break;
		}

		header( 'Location: ' . $to, true, 301 );
		exit();
	}
}
