<?php
namespace SlimSEO;

class Migration {

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

	}
}

