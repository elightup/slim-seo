<?php
namespace SlimSEO;

class Migration {

	/**
	 * Number of posts being processed in 1 call
	 * @var int
	 */
	public $threshold = 20;

	public function __construct() {
		add_action( 'wp_ajax_migrate_yoast', [ $this, 'handle_ajax' ] );
	}

	public function handle_ajax() {
		check_ajax_referer( 'process' );

		$restart = isset( $_POST['restart'] ) ? intval( $_POST['restart'] ) : 0;
		$page    = isset( $_POST['page'] ) ? $_POST['page'] : '';

		// If restart the process, reset session and send "continue" command
		if ( $restart ) {
			do_action( 'tv_import_before_process' );

			session_start();
			$_SESSION['processed'] = 0;

			wp_send_json_success( array(
				'message' => '',
				'type'    => 'continue',
			) );
		}

		$posts = $this->get_posts();
		if ( ! $posts ) {
			wp_send_json_success( array(
				'message' => '',
				'type'    => 'done',
			) );
		}

		wp_send_json_success( array(
			'message' => sprintf( __( 'Processed %d posts', 'slim-seo' ), count( $posts ) ),
			'type'    => 'continue',
		) );
	}

	private function get_posts() {
		$min = isset( $_SESSION['processed'] ) ? $_SESSION['processed'] : 0;
		$offset = $min + $this->threshold - 1;

		session_start();

		$_SESSION['processed'] = $offset;

		$posts = new WP_Query( [
			'post_type'      => 'post',
			'posts_per_page' => $threshold,
			'no_found_rows'  => true,
			'fields'         => 'ids',
			'offset'         => $offset,
		] );
		if( $posts->have_posts() ) {
			return false;
		}
		return $posts->found_posts;
	}
}

