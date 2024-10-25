<?php
namespace SlimSEO\Migration;

use SlimSEO\Helpers\Data;
use SlimSEO\Migration\Sources\Source;

class Migration {
	/**
	 * Number of posts being processed in 1 call
	 * @var int
	 */
	private $threshold = 10;

	/**
	 * The source object to do the migration.
	 * @var Source
	 */
	private $source;

	public function setup() {
		add_action( 'wp_ajax_ss_prepare_migration', [ $this, 'prepare_migration' ] );
		add_action( 'wp_ajax_ss_reset_counter', [ $this, 'reset_counter' ] );
		add_action( 'wp_ajax_ss_migrate_posts', [ $this, 'migrate_posts' ] );
		add_action( 'wp_ajax_ss_migrate_terms', [ $this, 'migrate_terms' ] );
		add_action( 'wp_ajax_ss_migrate_redirects', [ $this, 'migrate_redirects' ] );
	}

	public function prepare_migration(): void {
		check_ajax_referer( 'migrate' );

		session_start();
		$source_id             = $this->get_source_id();
		$_SESSION['source_id'] = $source_id;
		$this->set_source( $source_id );
		$this->check_activation( $source_id );

		wp_send_json_success();
	}

	private function get_source_id(): string {
		$source_id = sanitize_text_field( filter_input( INPUT_GET, 'source_id' ) );
		if ( empty( $source_id ) ) {
			wp_send_json_error( __( 'No platforms selected', 'slim-seo' ), 400 );
		}
		return $source_id;
	}

	private function set_source( string $source_id ): void {
		$this->source = Factory::make( $source_id );
	}

	private function check_activation( string $source_id ): void {
		if ( $this->source->is_activated() ) {
			return;
		}
		$sources = Data::get_migration_sources();

		// Translators: %s is the plugin name.
		wp_send_json_error( sprintf( __( 'Please activate %s plugin to use this feature. You can deactivate it after migration.', 'slim-seo' ), $sources[ $source_id ] ), 400 );
	}

	public function reset_counter(): void {
		session_start();
		$_SESSION['processed'] = 0;

		wp_send_json_success( [
			'message' => '',
			'type'    => 'continue',
		] );
	}

	public function migrate_posts(): void {
		session_start();
		$source_id = sanitize_text_field( $_SESSION['source_id'] ?? '' );
		$this->set_source( $source_id );
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

		if ( isset( $_SESSION['processed'] ) ) {
			$_SESSION['processed'] += count( $posts );
		}

		wp_send_json_success( [
			// Translators: %d is the number of processed posts.
			'message' => sprintf( __( 'Processed %d posts...', 'slim-seo' ), intval( $_SESSION['processed'] ?? 0 ) ),
			'type'    => 'continue',
		] );
	}

	public function migrate_terms(): void {
		session_start();
		$source_id = sanitize_text_field( $_SESSION['source_id'] ?? '' );
		$this->set_source( $source_id );
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

		if ( isset( $_SESSION['processed'] ) ) {
			$_SESSION['processed'] += count( $terms );
		}

		wp_send_json_success( [
			// Translators: %d is the number of processed items.
			'message' => sprintf( __( 'Processed %d terms...', 'slim-seo' ), intval( $_SESSION['processed'] ?? 0 ) ),
			'type'    => 'continue',
		] );
	}

	public function migrate_redirects() {
		session_start();
		$source_id = sanitize_text_field( $_SESSION['source_id'] ?? '' );
		$this->set_source( $source_id );
		$count = $this->source->migrate_redirects();

		if ( empty( $count ) ) {
			wp_send_json_success( [
				'message' => '',
			] );
		}

		wp_send_json_success( [
			// Translators: %d is the number of migrated redirects.
			'message' => sprintf( __( 'Migrated %d redirects...', 'slim-seo' ), $count ),
		] );
	}

	private function migrate_post( int $post_id ) {
		$this->source->migrate_post( $post_id );
	}

	private function migrate_term( int $term_id ) {
		$this->source->migrate_term( $term_id );
	}

	private function get_posts(): array {
		$query = new \WP_Query( [
			'post_type'      => array_keys( Data::get_post_types() ),
			'post_status'    => 'any',
			'posts_per_page' => $this->threshold,
			'no_found_rows'  => true,
			'fields'         => 'ids',
			'offset'         => intval( $_SESSION['processed'] ?? 0 ),
		] );

		return $query->posts;
	}

	private function get_terms(): array {
		return get_terms( [
			'taxonomy'   => array_keys( Data::get_taxonomies() ),
			'hide_empty' => false,
			'fields'     => 'ids',
			'number'     => $this->threshold,
			'offset'     => intval( $_SESSION['processed'] ?? 0 ),
		] );
	}
}
