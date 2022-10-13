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

		return $log_404->get_list( sanitize_text_field( $order_by ), sanitize_text_field( $order ), intval( $limit ), intval( $offset ) );
	}

	public static function handle() {
		if ( ! is_404() ) {
			return;
		}

		$settings = Helper::get_settings();

		if ( $settings['enable_404_logs'] ) {
			$http_host   = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$request_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$http_host}{$request_uri}";
			$log_404     = new Log();
			$log         = $log_404->get_log_by_url( $request_url );
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
