<?php
namespace SlimSEO\Redirection;

use SlimSEO\Redirection\Database\Redirects as DbRedirects;

class Redirection {
	protected $db_redirects;

	public function __construct( DbRedirects $db_redirects ) {
		$this->db_redirects = $db_redirects;

		add_action( 'template_redirect', [ $this, 'redirect' ], 1 );
		add_action( 'template_redirect', [ $this, 'redirect_www' ], 5 );
		add_action( 'template_redirect', [ $this, 'auto_redirection' ], 10 );
		add_filter( 'user_trailingslashit', [ $this, 'force_trailing_slash' ], 999 );
	}

	public function redirect() {
		$redirects = $this->db_redirects->list();

		if ( empty( $redirects ) ) {
			return;
		}

		$http_host   = $_SERVER['HTTP_HOST'] ?? ''; // @codingStandardsIgnoreLine.
		$request_uri = $_SERVER['REQUEST_URI'] ?? ''; // @codingStandardsIgnoreLine.
		$request_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$http_host}{$request_uri}";
		$request_url = Helper::normalize_url( $request_url );
		$request_url = strtolower( $request_url );

		foreach ( $redirects as $redirect ) {
			if ( empty( $redirect['enable'] ) ) {
				continue;
			}

			$current_url = $request_url;

			if ( ! empty( $redirect['ignoreParameters'] ) ) {
				$url_parts   = explode( '?', $current_url );
				$current_url = rtrim( $url_parts[0], '/' );
			}

			$from            = $redirect['from'];
			$to              = $redirect['to'];
			$should_redirect = false;

			switch ( $redirect['condition'] ) {
				case 'regex':
					$regex = '/' . preg_quote( $from, '/' ) . '/i';

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

	public function redirect_www() {
		$redirect_www = Settings::get( 'redirect_www' );

		if ( ! $redirect_www ) {
			return;
		}

		$should_redirect = false;
		$http_host       = $_SERVER['HTTP_HOST'] ?? ''; // @codingStandardsIgnoreLine.
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

		$request_uri  = $_SERVER['REQUEST_URI'] ?? ''; // @codingStandardsIgnoreLine.
		$redirect_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$http_host}{$request_uri}";
		$redirect_url = Helper::normalize_url( $redirect_url );

		header( 'Location: ' . $redirect_url, true, 301 );
		exit();
	}

	public function auto_redirection() {
		if ( ! Settings::get( 'auto_redirection' ) ) {
			return;
		}

		$this->attachment_redirect();
		$this->author_redirect();
		$this->hierarchical_post_slug_changed_redirect();
	}

	protected function attachment_redirect() {
		if ( ! is_attachment() ) {
			return;
		}

		$destination = wp_get_attachment_url( get_queried_object_id() );

		wp_safe_redirect( esc_url( $destination ), 301, 'Slim SEO' );
		die;
	}

	protected function author_redirect() {
		if ( ! is_author() ) {
			return;
		}

		$destination = '';

		if ( ! have_posts() ) {
			$destination = home_url( '/' );
		}
		// If the website has only one user.
		$users = get_users( [
			'number' => 2,
			'fields' => 'ID',
		] );

		if ( 1 === count( $users ) ) {
			$destination = home_url( '/' );
		}

		if ( $destination ) {
			wp_safe_redirect( esc_url( $destination ), 301, 'Slim SEO' );
			die;
		}
	}

	protected function hierarchical_post_slug_changed_redirect() {
		if ( ! is_404() ) {
			return;
		}

		global $wpdb;

		$http_host   = $_SERVER['HTTP_HOST'] ?? ''; // @codingStandardsIgnoreLine.
		$request_uri = $_SERVER['REQUEST_URI'] ?? ''; // @codingStandardsIgnoreLine.
		$request_url = ( Helper::is_ssl() ? 'https' : 'http' ) . "://{$http_host}{$request_uri}";
		$request_url = Helper::normalize_url( $request_url );
		$request_url = strtolower( $request_url );
		$post_id     = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = '_ss_old_permalink' AND meta_value = %s",
				$request_url
			)
		);

		if ( empty( $post_id ) ) {
			return;
		}

		$post = get_permalink( $post_id );

		wp_safe_redirect( $post, 301 );
		exit;
	}

	public function force_trailing_slash( string $url ) : string {
		if ( ! Settings::get( 'force_trailing_slash' ) ) {
			return $url;
		}

		$path = wp_parse_url( $url, PHP_URL_PATH );
		$ext  = pathinfo( $path, PATHINFO_EXTENSION );

		if ( empty( $ext ) ) {
			$url = trailingslashit( $url );
		}

		return $url;
	}
}
