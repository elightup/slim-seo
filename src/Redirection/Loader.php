<?php
namespace SlimSEO\Redirection;

class Loader {
	protected $db_redirects;
	protected $db_log;

	public function setup() {
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

		add_action( 'post_updated', [ $this, 'check_for_changed_slugs' ], 10, 3 );
		add_action( 'init', [ $this, 'setup_wp_schedule' ] );
		add_action( SLIM_SEO_DELETE_404_LOGS_ACTION, [ $this, 'run_wp_schedule' ] );
		add_action( 'slim_seo_deactivate', [ $this, 'deactivate' ] );
	}

	public function check_for_changed_slugs( $post_id, $post, $post_before ) {
		if ( ! Settings::get( 'auto_redirection' ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );
		// If post type is not hierarchical then leave it because WP already handles slugs changed
		if ( ! is_post_type_hierarchical( $post_type ) ) {
			return;
		}
		// If the slug has not changed
		if ( $post->post_name === $post_before->post_name ) {
			return;
		}
		// Check current post
		$permalink        = Helper::normalize_url( get_permalink( $post ) );
		$permalink_before = Helper::normalize_url( get_permalink( $post_before ) );

		Helper::save_old_permalink( $post_id, $permalink, $permalink_before );

		// Check its children posts
		$children_posts = Helper::get_children_posts( $post_id );

		if ( empty( $children_posts ) ) {
			return;
		}

		foreach ( $children_posts as $child_post ) {
			$child_permalink        = Helper::normalize_url( get_permalink( $child_post ) );
			$child_permalink_before = str_replace( $post->post_name, $post_before->post_name, $child_permalink );

			Helper::save_old_permalink( $child_post->ID, $child_permalink, $child_permalink_before );
		}
	}

	public function setup_wp_schedule() {
		$days = (int) Settings::get( 'auto_delete_404_logs' );

		if ( Settings::get( 'enable_404_logs' ) && $days !== -1 ) {
			if ( ! wp_next_scheduled( SLIM_SEO_DELETE_404_LOGS_ACTION ) ) {
				wp_schedule_event( time(), 'daily', SLIM_SEO_DELETE_404_LOGS_ACTION );
			}
		} else {
			wp_clear_scheduled_hook( SLIM_SEO_DELETE_404_LOGS_ACTION );
		}
	}

	public function run_wp_schedule() {
		$days = (int) Settings::get( 'auto_delete_404_logs' );

		if ( $days !== -1 && $this->db_log->table_exists() ) {
			$this->db_log->delete_older_logs( $days );
		}
	}

	public function deactivate() {
		wp_clear_scheduled_hook( SLIM_SEO_DELETE_404_LOGS_ACTION );
	}
}
