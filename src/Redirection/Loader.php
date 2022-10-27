<?php
namespace SlimSEO\Redirection;

class Loader {
	protected $db_redirects;
	protected $db_log;

	public function __construct() {
		$this->db_redirects = new Database\Redirects;
		$this->db_log       = new Database\Log404;

		if ( is_admin() ) {
			new Settings( $this->db_log );
		} else {
			new Redirection( $this->db_redirects );
			new Redirection404( $this->db_log );
		}

		new Api\Redirects( $this->db_redirects );
		new Api\Log404( $this->db_log );

		add_action( 'init', [ $this, 'init' ] );
		add_action( SLIM_SEO_DELETE_404_LOGS_ACTION, [ $this, 'run_schedule' ] );
		add_action( 'slim_seo_deactivate', [ $this, 'deactivate' ] );
	}

	public function init() {
		if ( ! wp_next_scheduled( SLIM_SEO_DELETE_404_LOGS_ACTION ) ) {
			wp_schedule_event( time(), 'daily', SLIM_SEO_DELETE_404_LOGS_ACTION );
		}
	}

	public function run_schedule() {
		$days = (int) Settings::get( 'automatically_delete_404_logs' );

		if ( ! $days ) {
			return;
		}

		$this->db_log->delete_older_logs( $days );
	}

	public function deactivate() {
		wp_clear_scheduled_hook( SLIM_SEO_DELETE_404_LOGS_ACTION );
	}
}
