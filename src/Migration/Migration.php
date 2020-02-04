<?php
namespace SlimSEO\Migration;
use SlimSEO\Helper as Helper;

class Migration {

	/**
	 * Number of posts being processed in 1 call
	 * @var int
	 */
	public $threshold = 10;

	public function setup() {
		session_start();
		add_action( 'wp_ajax_before_migration', [ $this, 'handle_before_migration' ] );
		add_action( 'wp_ajax_migrate_posts', [ $this, 'handle_posts_migration' ] );
		add_action( 'wp_ajax_migrate_terms', [ $this, 'handle_terms_migration' ] );
	}

	public function set_replacer( $platform ) {
		$_SESSION['replacer'] = ReplacerFactory::make( $platform );
	}

	public function handle_before_migration() {
		check_ajax_referer( 'migrate' );

		$restart = isset( $_POST['restart'] ) ? intval( $_POST['restart'] ) : 0;

		// Reset the session and prepare before migration.
		if ( $restart ) {

			// Reset the session variable to default for the new migration.
			$_SESSION['processed'] = 0;
			$_SESSION['replacer'] = null;

			$platform = isset( $_POST['platform'] ) ? sanitize_text_field( $_POST['platform'] ) : '';

			if ( empty( $platform ) ) {
				wp_send_json_error( __( 'No platforms selected', 'slim-seo' ) );
			}

			$this->set_replacer( $platform );

			$is_plugin_activation = $_SESSION['replacer']->is_plugin_activation();

			if ( ! $is_plugin_activation ) {
				$platforms = Helper::get_migration_platforms();
				wp_send_json_error( sprintf( __( 'Please activate %s plugin to use this feature. You can deactivate it after migration.', 'slim-seo' ), $platforms[ $platform ] ) );
			}

			// reset session and send "continue" command to start migrating.
			wp_send_json_success( array(
				'message' => '',
				'type'    => 'continue',
			) );
		}
	}

	public function handle_posts_migration() {
		$posts = $this->get_posts();
		if ( ! $posts ) {
			wp_send_json_success( array(
				'message' => '',
				'type'    => 'done',
			) );
		}
		$processed = $_SESSION['processed'] + count( $posts ) - $this->threshold;
		foreach( $posts as $post_id ) {
			$this->migrate_post( $post_id );
		}

		wp_send_json_success( array(
			'message' => sprintf( __( 'Processed %d posts...', 'slim-seo' ), $processed ),
			'posts'   => $processed,
			'type'    => 'continue',
		) );
	}

	public function handle_terms_migration() {
		$restart = isset( $_POST['restart'] ) ? intval( $_POST['restart'] ) : 0;
		// Reset processed session variable after posts migration
		if ( $restart ) {
			$_SESSION['processed'] = 0;
			wp_send_json_success( array(
				'message' => '',
				'type'    => 'continue',
			) );
		}

		$terms = $_SESSION['replacer']->get_terms( $this->threshold );

		if ( ! $terms ) {
			wp_send_json_success( array(
				'message' => '',
				'type'    => 'done',
			) );
		}

		$processed = $_SESSION['processed'] + count( $terms ) - $this->threshold;

		foreach( $terms as $term_id => $term ) {
			$this->migrate_term( $term_id, $term );
		}

		wp_send_json_success( array(
			'message' => sprintf( __( 'Processed %d terms...', 'slim-seo' ), $processed ),
			'posts'   => $processed,
			'type'    => 'continue',
		) );
	}

	private function migrate_post( $post_id ) {
		$_SESSION['replacer']->replace_post( $post_id );
	}

	private function migrate_term( $term_id, $term ) {
		$_SESSION['replacer']->replace_term( $term_id, $term );
	}

	private function get_posts() {
		$offset                = isset( $_SESSION['processed'] ) ? $_SESSION['processed'] : 0;
		$_SESSION['processed'] = $_SESSION['processed'] + $this->threshold;

		$post_types = Helper::get_post_types();
		$posts = new \WP_Query( [
			'post_type'      => $post_types,
			'post_status'    => ['publish', 'draft'],
			'posts_per_page' => $this->threshold,
			'no_found_rows'  => true,
			'fields'         => 'ids',
			'offset'         => $offset,
		] );

		if( ! $posts->have_posts() ) {
			return false;
		}
		return $posts->posts;
	}
}
