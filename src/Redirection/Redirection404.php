<?php
namespace SlimSEO\Redirection;

use SlimSEO\Redirection\Database\Log404 as DbLog;

class Redirection404 {
	public function __construct() {
		if ( ! Settings::get( 'enable_404_logs' ) ) {
			return;
		}

		add_action( 'template_redirect', [ $this, 'log' ], 1 );
		add_action( 'template_redirect', [ $this, 'redirect' ], 2 );
	}

	public function log() {
		if ( ! is_404() ) {
			return;
		}

		$http_host   = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$request_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$http_host}{$request_uri}";
		$db_log      = new DbLog();
		$log         = $db_log->get_log_by_url( $request_url );
		$now         = current_datetime()->format( 'Y-m-d H:i:s' );

		if ( empty( $log ) ) {
			$db_log->add( [
				'url'        => $request_url,
				'hit'        => 1,
				'created_at' => $now,
				'updated_at' => $now,
			] );
		} else {
			$log['hit']        = intval( $log['hit'] ) + 1;
			$log['updated_at'] = $now;

			$db_log->update( $log );
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
