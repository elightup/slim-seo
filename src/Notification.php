<?php
namespace SlimSEO;

class Notification {
	private $messages = [];

	public function __construct() {
		add_action( 'admin_notices', [ $this, 'notice' ] );
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
		<div class="notice notice-error">
			<p><strong><?php esc_html_e( 'Slim SEO:', 'slim-seo' ); ?></strong></p>
			<p><?php echo wp_kses( implode( '<br>', $this->messages ), $allowed_tags ); ?></p>
		</div>
		<?php
	}

	private function get_messages() {
		$sections = [
			'permalink',
			'visibility',
			'rss',
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
}
