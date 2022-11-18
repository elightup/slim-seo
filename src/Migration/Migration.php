<?php
namespace SlimSEO\Migration;

class Migration {
	/**
	 * Number of posts being processed in 1 call
	 * @var int
	 */
	public $threshold = 10;

	public function setup() {
		add_action( 'wp_ajax_ss_prepare_migration', [ $this, 'prepare_migration' ] );
		add_action( 'wp_ajax_ss_reset_counter', [ $this, 'reset_counter' ] );
		add_action( 'wp_ajax_ss_migrate_posts', [ $this, 'migrate_posts' ] );
		add_action( 'wp_ajax_ss_migrate_terms', [ $this, 'migrate_terms' ] );
		add_action( 'wp_ajax_ss_migrate_redirects', [ $this, 'migrate_redirects' ] );
	}

	public function prepare_migration() {
		check_ajax_referer( 'migrate' );

		session_start();
		$platform             = $this->get_platform();
		$_SESSION['platform'] = $platform;
		$this->set_replacer( $platform );
		$this->check_platform_activation( $platform );

		wp_send_json_success();
	}

	private function get_platform() {
		$platform = filter_input( INPUT_GET, 'platform', FILTER_SANITIZE_STRING );
		if ( empty( $platform ) ) {
			wp_send_json_error( __( 'No platforms selected', 'slim-seo' ), 400 );
		}
		return $platform;
	}

	private function set_replacer( $platform ) {
		$_SESSION['replacer'] = ReplacerFactory::make( $platform );
	}

	private function check_platform_activation( $platform ) {
		$is_activated = $_SESSION['replacer']->is_activated();
		if ( $is_activated ) {
			return;
		}
		$platforms = Helper::get_platforms();

		// Translators: %s is the plugin name.
		wp_send_json_error( sprintf( __( 'Please activate %s plugin to use this feature. You can deactivate it after migration.', 'slim-seo' ), $platforms[ $platform ] ), 400 );
	}

	public function reset_counter() {
		session_start();
		$_SESSION['processed'] = 0;

		wp_send_json_success( [
			'message' => '',
			'type'    => 'continue',
		] );
	}

	public function migrate_posts() {
		session_start();
		$this->set_replacer( $_SESSION['platform'] );
		$posts = $this->get_posts();
		if ( empty( $posts ) ) {
			wp_send_json_success( [
				'message' => '',
				'type'    => 'done',
			] );
		}
		foreach ( $posts as $post_id ) {
			$this->migrate_post( $post_id );
		}

		$_SESSION['processed'] += count( $posts );

		wp_send_json_success( [
			// Translators: %d is the number of processed posts.
			'message' => sprintf( __( 'Processed %d posts...', 'slim-seo' ), $_SESSION['processed'] ),
			'type'    => 'continue',
		] );
	}

	public function migrate_terms() {
		session_start();
		$this->set_replacer( $_SESSION['platform'] );
		$terms = $this->get_terms();

		if ( empty( $terms ) ) {
			wp_send_json_success( [
				'message' => '',
				'type'    => 'done',
			] );
		}

		foreach ( $terms as $term_id ) {
			$this->migrate_term( $term_id );
		}

		$_SESSION['processed'] += count( $terms );

		wp_send_json_success( [
			// Translators: %d is the number of processed items.
			'message' => sprintf( __( 'Processed %d terms...', 'slim-seo' ), $_SESSION['processed'] ),
			'type'    => 'continue',
		] );
	}

	public function migrate_redirects() {
		session_start();
		$this->set_replacer( $_SESSION['platform'] );
		$migrated_redirects = $_SESSION['replacer']->migrate_redirects();

		if ( empty( $migrated_redirects ) ) {
			wp_send_json_success( [
				'message' => '',
			] );
		}

		wp_send_json_success( [
			// Translators: %d is the number of migrated redirects.
			'message' => sprintf( __( 'Migrated %d redirects...', 'slim-seo' ), $migrated_redirects ),
		] );
	}

	private function migrate_post( $post_id ) {
		$_SESSION['replacer']->replace_post( $post_id );
	}

	private function migrate_term( $term_id ) {
		$_SESSION['replacer']->replace_term( $term_id );
	}

	private function get_posts() {
		$post_types = Helper::get_post_types();
		$posts      = new \WP_Query( [
			'post_type'      => $post_types,
			'post_status'    => [ 'publish', 'draft' ],
			'posts_per_page' => $this->threshold,
			'no_found_rows'  => true,
			'fields'         => 'ids',
			'offset'         => $_SESSION['processed'],
		] );

		return $posts->posts;
	}

	private function get_terms() {
		$taxonomies = Helper::get_taxonomies();
		return get_terms( [
			'taxonomy'   => $taxonomies,
			'hide_empty' => false,
			'fields'     => 'ids',
			'number'     => $this->threshold,
			'offset'     => $_SESSION['processed'],
		] );
	}
}
