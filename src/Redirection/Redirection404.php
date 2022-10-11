<?php
namespace SlimSEO\Redirection;

use SlimSEO\Redirection\Database\Log404 as Log;

class Redirection404 {
	public static function get_total_logs() : int {
		$log_404 = new Log();

		return $log_404->get_total();
	}

	public static function get_logs_list( string $order_by, string $order, int $limit, int $offset ) : array {
		$log_404 = new Log();

		return $log_404->get_list( $order_by, $order, $limit, $offset );
	}

	public static function handle() {
		if ( ! is_404() ) {
			return;
		}

		$settings = Helper::get_settings();

		if ( $settings['enable_404_logs'] ) {
			$request_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
			$log_404     = new Log();
			$log         = $log_404->get( $request_url, 'url' );
			$now         = current_datetime()->format( 'Y-m-d H:i:s' );

			if ( empty( $log ) ) {
				$log_404->add( [
					'url'        => $request_url,
					'hit'        => 1,
					'created_at' => $now,
					'updated_at' => $now,
				] );
			} else {
				$log['hit']        = intval( $log['hit'] ) + 1;
				$log['updated_at'] = $now;

				$log_404->update( $log );
			}
		}

		$redirect_to = $settings['redirect_404_to'];

		if ( $redirect_to ) {
			switch ( $redirect_to ) {
				case 'custom':
					$to = $settings['redirect_404_to_url'];
					break;

				default:
					$to = home_url();
					break;
			}

			header( 'Location: ' . $to, true, 301 );
			exit();
		}
	}
}
