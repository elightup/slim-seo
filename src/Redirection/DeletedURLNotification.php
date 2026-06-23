<?php
namespace SlimSEO\Redirection;

class DeletedURLNotification {
	const DELETED_URLS_OPTION_NAME = 'ss_redirection_deleted_urls';

	public function __construct() {
		add_action( 'wp_trash_post', [ $this, 'trash_post' ] );
		add_action( 'pre_delete_term', [ $this, 'delete_term' ] );
		add_action( 'post_updated', [ $this, 'post_updated' ], 10, 3 );

		add_action( 'admin_notices', [ $this, 'notifications' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'wp_ajax_slim_seo_redirection_dismiss_deleted_url_notification', [ $this, 'dismiss' ] );
	}

	public function trash_post( int $post_id ): void {
		$post = get_post( $post_id );

		if ( ! $post || 'publish' !== $post->post_status ) {
			return;
		}

		self::add( get_permalink( $post_id ) );
	}

	public function delete_term( int $term_id ): void {
		$link = get_term_link( $term_id );

		if ( is_wp_error( $link ) ) {
			return;
		}

		self::add( $link, 'term' );
	}

	public function post_updated( int $post_id, $post_after, $post_before ): void {
		if ( 'draft' !== $post_before->post_status || 'publish' !== $post_after->post_status ) {
			return;
		}

		self::delete_url( get_permalink( $post_id ) );
	}

	public function notifications(): void {
		if ( ! $this->has_permission() ) {
			return;
		}

		$urls = self::list();

		if ( empty( $urls ) ) {
			return;
		}

		foreach ( $urls as $url_index => $url_data ) {
			?>
			<div class="ss-redirection-deleted-url-notification notice notice-warning is-dismissible" data-index="<?php echo esc_attr( $url_index ); ?>">
				<p><strong><?php esc_html_e( 'Slim SEO Redirection:', 'slim-seo' ); ?></strong></p>
				<p>
					<?php
					printf(
						wp_kses_post(
							/* translators: 1: content type, 2: deleted URL, 3: redirect URL, 4: link text. */
							__( 'A %1$s has been moved to trash. You may redirect <code>%2$s</code> to <a href="%3$s">%4$s</a>.', 'slim-seo' )
						),
						esc_html( $url_data['type'] ),
						esc_html( $url_data['url'] ),
						esc_url( admin_url( "options-general.php?page=slim-seo&deleted_url_index={$url_index}#redirection" ) ),
						esc_html__( 'a new URL', 'slim-seo' )
					);
					?>
				</p>
			</div>
			<?php
		}
	}

	public function enqueue(): void {
		if ( ! $this->has_permission() ) {
			return;
		}

		wp_enqueue_script( 'slim-seo-redirection-deleted-url-notification', SLIM_SEO_URL . 'js/redirection/deleted-url-notification.js', [], filemtime( SLIM_SEO_DIR . 'js/redirection/deleted-url-notification.js' ), true );
		wp_localize_script( 'slim-seo-redirection-deleted-url-notification', 'SSRedirectionDeletedURLNotification', [ 'nonce' => wp_create_nonce( 'slim_seo_dismiss_deleted_url_notification' ) ] );
	}

	public function dismiss(): void {
		if ( ! $this->has_permission() ) {
			wp_send_json_error();
		}

		check_ajax_referer( 'slim_seo_dismiss_deleted_url_notification', 'nonce' );

		if ( isset( $_POST['index'] ) && is_numeric( $_POST['index'] ) ) {
			self::delete_index( (int) $_POST['index'] );
		}

		wp_send_json_success();
	}

	private static function list(): array {
		return get_option( self::DELETED_URLS_OPTION_NAME ) ?: [];
	}

	private static function update( array $urls ): void {
		update_option( self::DELETED_URLS_OPTION_NAME, $urls );
	}

	private static function add( string $url, string $type = 'post' ): void {
		$urls = self::list();

		foreach ( $urls as $url_data ) {
			if ( $url_data['url'] === $url ) {
				return;
			}
		}

		$urls[] = [
			'url'  => $url,
			'type' => $type,
		];

		self::update( $urls );
	}

	public static function delete_url( string $url ): void {
		$urls  = self::list();
		$found = false;

		foreach ( $urls as $url_index => $url_data ) {
			if ( $url_data['url'] === $url ) {
				unset( $urls[ $url_index ] );

				$found = true;

				break;
			}
		}

		if ( $found ) {
			self::update( $urls );
		}
	}

	private static function delete_index( int $index ): void {
		$urls = self::list();

		if ( isset( $urls[ $index ] ) ) {
			unset( $urls[ $index ] );

			self::update( $urls );
		}
	}

	public static function get_url( int $index ): string {
		$urls = self::list();

		return isset( $urls[ $index ] ) ? ( $urls[ $index ]['url'] ?? '' ) : '';
	}

	private function has_permission(): bool {
		return current_user_can( 'manage_options' );
	}
}
