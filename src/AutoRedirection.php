<?php
namespace SlimSEO;

class AutoRedirection {
	public function setup() {
		add_action( 'template_redirect', [ $this, 'redirect' ] );
	}

	public function redirect() {
		$destination = $this->get_destination();
		if ( $destination ) {
			wp_safe_redirect( esc_url( $destination ), 301, 'Slim SEO' );
			die;
		}
	}

	private function get_destination() {
		if ( is_attachment() ) {
			return $this->get_attachment_destination();
		}

		if ( is_author() ) {
			return $this->get_author_destination();
		}

		return null;
	}

	/**
	 * Get redirect destination for attachment page, which is file URL.
	 *
	 * @return string
	 */
	private function get_attachment_destination() {
		return wp_get_attachment_url( get_queried_object_id() );
	}

	/**
	 * Get redirect destination for author page.
	 * If author has no posts, or the website has only one user, then redirect author page to homepage.
	 *
	 * @return string
	 */
	private function get_author_destination() {
		// If no posts.
		if ( ! have_posts() ) {
			return home_url( '/' );
		}

		// If the website has only one user.
		$users = get_users( [
			'number' => 2,
			'fields' => 'ID',
		] );
		if ( 1 === count( $users ) ) {
			return home_url( '/' );
		}

		return null;
	}
}
