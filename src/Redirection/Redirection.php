<?php
namespace SlimSEO\Redirection;

use SlimSEO\Redirection\Database\Redirects as DbRedirects;

class Redirection {
	protected $db_redirects;

	public function __construct( DbRedirects $db_redirects ) {
		$this->db_redirects = $db_redirects;

		add_action( 'plugins_loaded', [ $this, 'redirect' ], 1 );
		add_filter( 'user_trailingslashit', [ $this, 'force_trailing_slash' ], 1000, 2 );
		add_action( 'plugins_loaded', [ $this, 'redirect_www' ], 2 );
	}

	public function redirect() {
		$redirects = $this->db_redirects->list();

		if ( empty( $redirects ) ) {
			return;
		}

		$http_host   = $_SERVER['HTTP_HOST'] ?: ''; // @codingStandardsIgnoreLine.
		$request_uri = $_SERVER['REQUEST_URI'] ?: ''; // @codingStandardsIgnoreLine.
		$request_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$http_host}{$request_uri}";
		$request_url = Helper::normalize_url( $request_url );
		$request_url = strtolower( $request_url );
		$request_url = rtrim( $request_url, '/' );

		foreach ( $redirects as $redirect ) {
			if ( empty( $redirect['enable'] ) ) {
				continue;
			}

			$current_url = $request_url;

			if ( ! empty( $redirect['ignoreParameters'] ) ) {
				$url_parts   = explode( '?', $current_url );
				$current_url = rtrim( $url_parts[0], '/' );
			}

			$from            = rtrim( $redirect['from'], '/' );
			$to              = $redirect['to'];
			$should_redirect = false;

			switch ( $redirect['condition'] ) {
				case 'regex':
					$regex = '/' . str_replace( '/', '\/', $from ) . '/i';

					if ( preg_match( $regex, $current_url ) ) {
						$to              = preg_replace( $regex, $to, $current_url );
						$should_redirect = true;
					}

					break;

				case 'contain':
					if ( false !== stripos( $current_url, $from ) ) {
						$should_redirect = true;
					}

					break;

				case 'start-with':
					if ( 0 === stripos( $current_url, $from ) ) {
						$should_redirect = true;
					}

					break;

				case 'end-with':
					if ( ( strlen( $current_url ) - strlen( $from ) ) === stripos( $current_url, $from ) ) {
						$should_redirect = true;
					}

					break;

				default:
					$from = filter_var( $from, FILTER_VALIDATE_URL ) ? $from : home_url( $from );

					if ( $current_url === $from ) {
						$should_redirect = true;
					}

					break;
			}

			if ( ! $should_redirect ) {
				continue;
			}

			switch ( $redirect['type'] ) {
				case 301:
					header( 'HTTP/1.1 301 Moved Permanently' );

					break;
				case 302:
					header( 'HTTP/1.1 302 Found' );

					break;
				case 307:
					header( 'HTTP/1.1 307 Temporary Redirect' );

					break;
				case 410:
					header( 'HTTP/1.1 410 Content Deleted' );

					break;
				case 451:
					header( 'HTTP/1.1 451 Unavailable For Legal Reasons' );

					break;
			}

			$to = filter_var( $to, FILTER_VALIDATE_URL ) ? $to : home_url( $to );

			header( 'Location: ' . $to, true, (int) $redirect['type'] );
			exit();
		}
	}

	public function force_trailing_slash( string $string, string $type_of_url ) : string {
		if ( Settings::get( 'force_trailing_slash' ) ) {
			$string = trailingslashit( $string );
		}

		return $string;
	}

	public function redirect_www() {
		$redirect_www = Settings::get( 'redirect_www' );

		if ( ! $redirect_www ) {
			return;
		}

		$should_redirect = false;
		$http_host       = $_SERVER['HTTP_HOST'] ?: ''; // @codingStandardsIgnoreLine.
		$http_host       = strtolower( $http_host );

		if ( 'www-to-non' === $redirect_www && false !== stripos( $http_host, 'wwww' ) ) {
			$http_host       = substr( $http_host, 4 );
			$should_redirect = true;
		} elseif ( 'non-to-www' === $redirect_www && false === stripos( $http_host, 'wwww' ) ) {
			$http_host       = 'www.' . $http_host;
			$should_redirect = true;
		}

		if ( ! $should_redirect ) {
			return;
		}

		$request_uri  = $_SERVER['REQUEST_URI'] ?: ''; // @codingStandardsIgnoreLine.
		$redirect_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$http_host}{$request_uri}";
		$redirect_url = Helper::normalize_url( $redirect_url );

		header( 'Location: ' . $redirect_url, true, 301 );
		exit();
	}
}
