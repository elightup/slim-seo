<?php
namespace SlimSEO;

class Notification {
	private $messages = [];

	public function setup() {
		if ( $this->is_dismissed() ) {
			return;
		}

		add_action( 'admin_notices', [ $this, 'notice' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'wp_ajax_slim_seo_dismiss_notification', [ $this, 'dismiss' ] );
	}

	public function enqueue() {
		wp_enqueue_script( 'slim-seo-notification', SLIM_SEO_URL . 'js/notification.js', [], SLIM_SEO_VER, true );
		wp_localize_script( 'slim-seo-notification', 'SlimSEONotification', [ 'nonce' => wp_create_nonce( 'dismiss' ) ] );
	}

	public function dismiss() {
		check_ajax_referer( 'dismiss', 'nonce' );
		$option = get_option( 'slim_seo', [] );
		$option['notification_dismissed'] = 1;
		update_option( 'slim_seo', $option );
		wp_send_json_success();
	}

	public function notice() {
		$this->get_messages();
		if ( ! $this->messages ) {
			return;
		}
		$allowed_tags = [
			'a'  => [
				'href'   => [],
				'target' => [],
			],
			'br' => [],
		];
		?>
		<div id="slim-seo-notification" class="notice notice-error is-dismissible">
			<p><strong><?php esc_html_e( 'Slim SEO:', 'slim-seo' ); ?></strong></p>
			<p><?php echo wp_kses( implode( '<br>', $this->messages ), $allowed_tags ); ?></p>
		</div>
		<?php
	}

	private function is_dismissed() {
		$option = get_option( 'slim_seo' );
		return ! empty( $option['notification_dismissed'] );
	}

	private function get_messages() {
		$sections = [
			'permalink',
			'visibility',
			'rss',
			'ssl',
		];
		foreach ( $sections as $section ) {
			$getter = "get_{$section}_message";
			$this->$getter();
		}
	}

	private function get_permalink_message() {
		if ( ! get_option( 'permalink_structure' ) ) {
			$this->messages[] = sprintf( __( 'You are not using pretty permalink structure. <a href="%s">Fix this &rarr;</a>', 'slim-seo' ), admin_url( 'options-permalink.php' ) );
		}
	}

	private function get_visibility_message() {
		if ( ! get_option( 'blog_public' ) ) {
			$this->messages[] = sprintf( __( 'Your site is not visible to search engines. <a href="%s">Fix this &rarr;</a>', 'slim-seo' ), admin_url( 'options-reading.php' ) );
		}
	}

	private function get_rss_message() {
		if ( ! get_option( 'rss_use_excerpt' ) ) {
			$this->messages[] = sprintf( __( 'Your RSS feed shows full text. <a href="%s">Fix this &rarr;</a>', 'slim-seo' ), admin_url( 'options-reading.php' ) );
		}
	}

	private function get_ssl_message() {
		if ( ! is_ssl() ) {
			$this->messages[] = sprintf( __( 'Your website does not use HTTPS. Please upgrade your host to fix this.', 'slim-seo' ), admin_url( 'options-reading.php' ) );
		}
	}
}
