<?php
namespace SlimSEO\Redirection;

class Redirects {
	public static function list() : array {
		$redirects = get_option( SLIM_SEO_REDIRECTION_REDIRECTS_OPTION_NAME );

		return ! empty( $redirects ) ? $redirects : [];
	}

	public static function is_exists( string $url ) : bool {
		$redirects = self::list();
		$home_url  = untrailingslashit( home_url() );
		$url       = html_entity_decode( str_replace( $home_url, '', $url ) );

		return count( array_filter( $redirects, function( $redirect ) use ( $url ) {
			return $redirect['from'] === $url;
		} ) ) > 0;
	}

	public static function update( array $redirect ) {
		$redirects   = self::list();
		$redirect_id = $redirect['id'] ?? -1;

		unset( $redirect['id'] );

		$home_url = untrailingslashit( home_url() );

		$redirect['from'] = html_entity_decode( str_replace( $home_url, '', $redirect['from'] ) );
		$redirect['to']   = html_entity_decode( str_replace( $home_url, '', $redirect['to'] ) );

		if ( -1 == $redirect_id ) {
			$redirects[] = $redirect;
		} else {
			$redirects[$redirect_id] = $redirect;
		}

		update_option( SLIM_SEO_REDIRECTION_REDIRECTS_OPTION_NAME, $redirects );
	}

	public static function delete( array $ids ) {
		$redirects = self::list();
		$redirects = array_filter( $redirects, function( $id ) use ( $ids ) {
			return ! in_array( $id, $ids );
		}, ARRAY_FILTER_USE_KEY );

		update_option( SLIM_SEO_REDIRECTION_REDIRECTS_OPTION_NAME, $redirects );
	}

	public static function handle() {
		$redirects = self::list();

		if ( empty( $redirects ) ) {
			return;
		}

		$request_url = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$request_url = rtrim( strtolower( urldecode( $request_url ) ), '/' );

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

			header( 'Location: ' . $to, true, (int)$redirect['type'] );
			exit();
		}
	}
}