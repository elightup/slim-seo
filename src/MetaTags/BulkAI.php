<?php
namespace SlimSEO\MetaTags;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WP_Query;
use WP_Term;

class BulkAI {
	private const REST_NS = 'slim-seo';

	/** Upper bound for batch size after filtering (avoids excessive load per request). */
	private const MAX_BATCH = 50;

	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ] );
	}

	public function enqueue(): void {
		wp_enqueue_style( 'slim-seo-bulk-ai', SLIM_SEO_URL . 'css/bulk-ai.css', [ 'wp-components' ], filemtime( SLIM_SEO_DIR . '/css/bulk-ai.css' ) );
	}

	public function register_routes(): void {
		register_rest_route( self::REST_NS, 'bulk-ai/chunk', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'process_chunk' ],
			'permission_callback' => fn () => current_user_can( 'manage_options' ),
			'args'                => [
				'phase'            => [
					'type'    => 'string',
					'enum'    => [ 'posts', 'terms' ],
					'default' => 'posts',
				],
				'offset'           => [
					'type'    => 'integer',
					'default' => 0,
					'minimum' => 0,
				],
				'post_types'       => [
					'type'    => 'array',
					'default' => [],
					'items'   => [ 'type' => 'string' ],
				],
				'taxonomies'       => [
					'type'    => 'array',
					'default' => [],
					'items'   => [ 'type' => 'string' ],
				],
				'skip_title'       => [
					'type'    => 'boolean',
					'default' => true,
				],
				'skip_description' => [
					'type'    => 'boolean',
					'default' => true,
				],
			],
		] );
	}

	/**
	 * Number of items to process per chunk. Not exposed in the UI; override with the `slim_seo_bulk_ai_batch_size` filter.
	 *
	 * @return int Between 1 and self::MAX_BATCH (inclusive).
	 */
	private function get_effective_batch_size(): int {
		$batch_size = (int) apply_filters( 'slim_seo_bulk_ai_batch_size', 10 );

		return max( 1, min( self::MAX_BATCH, $batch_size ) );
	}

	/**
	 * Process a single chunk of bulk AI generation.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function process_chunk( WP_REST_Request $request ) {
		$t0 = microtime( true );

		$offset = max( 0, (int) $request->get_param( 'offset' ) );
		$phase  = in_array( $request->get_param( 'phase' ), [ 'posts', 'terms' ], true )
			? $request->get_param( 'phase' )
			: 'posts';

		$post_types = array_filter( array_map( 'sanitize_key', (array) $request->get_param( 'post_types' ) ) );
		$taxonomies = array_filter( array_map( 'sanitize_key', (array) $request->get_param( 'taxonomies' ) ) );

		$skip_title       = (bool) $request->get_param( 'skip_title' );
		$skip_description = (bool) $request->get_param( 'skip_description' );

		if ( empty( $post_types ) && empty( $taxonomies ) ) {
			return new WP_Error(
				'slim_seo_bulk_ai_input',
				__( 'Please select at least one content type to generate meta data for.', 'slim-seo' ),
				[ 'status' => 400 ]
			);
		}

		// If we start in 'posts' with none selected, skip straight to terms — the early guard above ensures taxonomies is non-empty here.
		if ( 'posts' === $phase && empty( $post_types ) ) {
			$phase  = 'terms';
			$offset = 0;
		}

		if ( 'posts' === $phase ) {
			$outcome = $this->run_posts_chunk( $post_types, $offset, $skip_title, $skip_description );
		}

		if ( 'terms' === $phase ) {
			$outcome = $this->run_terms_chunk( $taxonomies, $offset, $skip_title, $skip_description );
		}

		return new WP_REST_Response( [
			'done'        => $outcome['done'],
			'next_phase'  => $outcome['next_phase'],
			'next_offset' => $outcome['next_offset'],
			'elapsed_ms'  => (int) round( ( microtime( true ) - $t0 ) * 1000 ),
			'batch_stats' => $outcome['batch_stats'],
			'log_entries' => $outcome['entries'],
		] );
	}

	/**
	 * Run one chunk of the posts phase and decide where the next chunk (if any) should resume.
	 *
	 * @param array $post_types The post types to process.
	 * @param int   $offset The offset.
	 * @param bool  $skip_title Whether to skip the title.
	 * @param bool  $skip_description Whether to skip the description.
	 * @return array The result of the posts phase.
	 */
	private function run_posts_chunk( array $post_types, int $offset, bool $skip_title, bool $skip_description ): array {
		$result = $this->process_posts_phase( $post_types, $offset, $skip_title, $skip_description, [], $this->empty_batch_stats() );

		return [
			'entries'     => $result['entries'],
			'batch_stats' => $result['batch_stats'],
			'next_phase'  => $result['has_more'] ? 'posts' : 'terms',
			'next_offset' => $result['has_more'] ? $offset + $result['count'] : 0,
			'done'        => false,
		];
	}

	/**
	 * Run one chunk of the terms phase and decide where the next chunk (if any) should resume.
	 *
	 * @param array $taxonomies The taxonomies to process.
	 * @param int   $offset The offset.
	 * @param bool  $skip_title Whether to skip the title.
	 * @param bool  $skip_description Whether to skip the description.
	 * @return array The result of the terms phase.
	 */
	private function run_terms_chunk( array $taxonomies, int $offset, bool $skip_title, bool $skip_description ): array {
		if ( empty( $taxonomies ) ) {
			return [
				'entries'     => [],
				'batch_stats' => $this->empty_batch_stats(),
				'next_phase'  => 'terms',
				'next_offset' => $offset,
				'done'        => true,
			];
		}

		$result = $this->process_terms_phase( $taxonomies, $offset, $skip_title, $skip_description, [], $this->empty_batch_stats() );

		return [
			'entries'     => $result['entries'],
			'batch_stats' => $result['batch_stats'],
			'next_phase'  => 'terms',
			'next_offset' => $result['has_more'] ? $offset + $result['count'] : $offset,
			'done'        => ! $result['has_more'],
		];
	}

	/**
	 * Process a chunk of posts.
	 *
	 * @param array $post_types The post types to process.
	 * @param int   $offset The offset.
	 * @param bool  $skip_title Whether to skip the title.
	 * @param bool  $skip_description Whether to skip the description.
	 * @param array $entries The entries.
	 * @param array $batch_stats The batch stats.
	 * @return array The result of the posts phase.
	 */
	private function process_posts_phase( array $post_types, int $offset, bool $skip_title, bool $skip_description, array $entries, array $batch_stats ): array {
		$batch = $this->get_effective_batch_size();
		$query = new WP_Query( [
			'post_type'              => $post_types,
			'post_status'            => 'any',
			'posts_per_page'         => $batch,
			'offset'                 => $offset,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );

		if ( ! $query->have_posts() ) {
			$this->push_entry( $entries, 'info', 'batch', 'System', __( 'No more posts to process.', 'slim-seo' ) );
			return [
				'entries'     => $entries,
				'batch_stats' => $batch_stats,
				'has_more'    => false,
				'count'       => 0,
			];
		}

		$ids = array_map( 'intval', $query->posts );

		foreach ( $ids as $post_id ) {
			$piece = $this->process_post( $post_id, $skip_title, $skip_description );
			$this->merge_batch_stats( $batch_stats, $piece['stats'] );
			array_push( $entries, ...$piece['entries'] );
		}

		return [
			'entries'     => $entries,
			'batch_stats' => $batch_stats,
			'has_more'    => true,
			'count'       => count( $ids ),
		];
	}

	/**
	 * Process a chunk of terms.
	 *
	 * @param array $taxonomies The taxonomies to process.
	 * @param int   $offset The offset.
	 * @param bool  $skip_title Whether to skip the title.
	 * @param bool  $skip_description Whether to skip the description.
	 * @param array $entries The entries.
	 * @param array $batch_stats The batch stats.
	 * @return array The result of the terms phase.
	 */
	private function process_terms_phase( array $taxonomies, int $offset, bool $skip_title, bool $skip_description, array $entries, array $batch_stats ): array {
		$batch = $this->get_effective_batch_size();
		$terms = get_terms( [
			'taxonomy'   => $taxonomies,
			'hide_empty' => false,
			'number'     => $batch,
			'offset'     => $offset,
			'orderby'    => 'term_id',
			'order'      => 'ASC',
		] );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			$this->push_entry( $entries, 'info', 'batch', 'System', __( 'No more terms to process.', 'slim-seo' ) );
			return [
				'entries'     => $entries,
				'batch_stats' => $batch_stats,
				'has_more'    => false,
				'count'       => 0,
			];
		}

		foreach ( $terms as $term ) {
			$piece = $this->process_term( $term, $skip_title, $skip_description );
			$this->merge_batch_stats( $batch_stats, $piece['stats'] );
			array_push( $entries, ...$piece['entries'] );
		}

		return [
			'entries'     => $entries,
			'batch_stats' => $batch_stats,
			'has_more'    => true,
			'count'       => count( $terms ),
		];
	}

	/**
	 * Process a single post.
	 *
	 * @param int  $post_id The post ID.
	 * @param bool $skip_title Whether to skip the title.
	 * @param bool $skip_description Whether to skip the description.
	 * @return array The result of the post phase.
	 */
	private function process_post( int $post_id, bool $skip_title, bool $skip_description ): array {
		$entries = [];
		$stats   = $this->empty_batch_stats();

		$post = get_post( $post_id );
		if ( ! $post ) {
			++$stats['errors'];
			$this->push_entry( $entries, 'error', 'post', (string) $post_id, __( 'Post not found, skipping.', 'slim-seo' ) );
			return [
				'entries' => $entries,
				'stats'   => $stats,
			];
		}

		$title = get_the_title( $post );
		$data  = get_post_meta( $post_id, 'slim_seo', true );
		$data  = is_array( $data ) ? $data : [];
		$orig  = $data;

		$ai = new AI();

		// Generate title.
		if ( $skip_title && ! empty( $orig['title'] ) ) {
			++$stats['skipped'];
			$this->push_entry( $entries, 'skip', 'post', $title, __( 'Title already exists, skipped.', 'slim-seo' ) );
		} else {
			$req = new WP_REST_Request( 'POST' );
			$req->set_param( 'title', $title );
			$req->set_param( 'content', '' );
			$req->set_param( 'object', [
				'type' => 'post',
				'ID'   => $post_id,
			] );
			$req->set_param( 'type', 'title' );
			$req->set_param( 'previousMetaByAI', $this->previous_meta( $skip_title, $orig['title'] ?? '' ) );
			$res = $ai->generate( $req );

			$status = $res['status'] ?? '';
			if ( $status === 'success' ) {
				++$stats['success'];
				$data['title'] = $res['message'];
				$this->push_entry( $entries, 'ok', 'post', $title, sprintf(
					/* translators: %s: generated title */
					__( 'Title: %s', 'slim-seo' ),
					$res['message']
				) );
			} else {
				++$stats['errors'];
				$this->push_entry( $entries, 'error', 'post', $title, sprintf(
					/* translators: %s: error message */
					__( 'Could not generate title: %s', 'slim-seo' ),
					$res['message'] ?? __( 'Unknown error', 'slim-seo' )
				) );
			}
		}

		// Generate description.
		if ( $skip_description && ! empty( $orig['description'] ) ) {
			++$stats['skipped'];
			$this->push_entry( $entries, 'skip', 'post', $title, __( 'Description already exists, skipped.', 'slim-seo' ) );
		} else {
			$req = new WP_REST_Request( 'POST' );
			$req->set_param( 'title', '' );
			$req->set_param( 'content', '' );
			$req->set_param( 'object', [
				'type' => 'post',
				'ID'   => $post_id,
			] );
			$req->set_param( 'type', 'description' );
			$req->set_param( 'previousMetaByAI', $this->previous_meta( $skip_description, $orig['description'] ?? '' ) );
			$res = $ai->generate( $req );

			$status = $res['status'] ?? '';
			if ( $status === 'success' ) {
				++$stats['success'];
				$data['description'] = $res['message'];
				$this->push_entry( $entries, 'ok', 'post', $title, sprintf(
					/* translators: %s: generated description */
					__( 'Description: %s', 'slim-seo' ),
					$res['message']
				) );
			} else {
				++$stats['errors'];
				$this->push_entry( $entries, 'error', 'post', $title, sprintf(
					/* translators: %s: error message */
					__( 'Could not generate description: %s', 'slim-seo' ),
					$res['message'] ?? __( 'Unknown error', 'slim-seo' )
				) );
			}
		}

		$data = array_filter( $data );
		if ( empty( $data ) ) {
			delete_post_meta( $post_id, 'slim_seo' );
		} else {
			update_post_meta( $post_id, 'slim_seo', $data );
		}

		return [
			'entries' => $entries,
			'stats'   => $stats,
		];
	}

	/**
	 * Process a single term.
	 *
	 * @param WP_Term $term The term.
	 * @param bool    $skip_title Whether to skip the title.
	 * @param bool    $skip_description Whether to skip the description.
	 * @return array The result of the term phase.
	 */
	private function process_term( WP_Term $term, bool $skip_title, bool $skip_description ): array {
		$entries = [];
		$stats   = $this->empty_batch_stats();
		$term_id = (int) $term->term_id;

		$data = get_term_meta( $term_id, 'slim_seo', true );
		$data = is_array( $data ) ? $data : [];
		$orig = $data;

		$raw_content = term_description( $term_id );
		$content     = wp_strip_all_tags( (string) $raw_content );
		if ( '' === $content ) {
			$content = $term->name;
		}

		$ai = new AI();

		// Generate title.
		if ( $skip_title && ! empty( $orig['title'] ) ) {
			++$stats['skipped'];
			$this->push_entry( $entries, 'skip', 'term', $term->name, __( 'Title already exists, skipped.', 'slim-seo' ) );
		} else {
			$req = new WP_REST_Request( 'POST' );
			$req->set_param( 'title', $term->name );
			$req->set_param( 'content', $content );
			$req->set_param( 'object', [
				'type' => 'term',
				'ID'   => $term_id,
			] );
			$req->set_param( 'type', 'title' );
			$req->set_param( 'previousMetaByAI', $this->previous_meta( $skip_title, $orig['title'] ?? '' ) );
			$res = $ai->generate( $req );

			$status = $res['status'] ?? '';
			if ( $status === 'success' ) {
				$data['title'] = $res['message'];
				$this->push_entry( $entries, 'ok', 'term', $term->name, sprintf(
					/* translators: %s: generated title */
					__( 'Title: %s', 'slim-seo' ),
					$res['message']
				) );
			} else {
				++$stats['errors'];
				$this->push_entry( $entries, 'error', 'term', $term->name, sprintf(
					/* translators: %s: error message */
					__( 'Could not generate title: %s', 'slim-seo' ),
					$res['message'] ?? __( 'Unknown error', 'slim-seo' )
				) );
			}
		}

		// Generate description.
		if ( $skip_description && ! empty( $orig['description'] ) ) {
			++$stats['skipped'];
			$this->push_entry( $entries, 'skip', 'term', $term->name, __( 'Description already exists, skipped.', 'slim-seo' ) );
		} else {
			$req = new WP_REST_Request( 'POST' );
			$req->set_param( 'title', '' );
			$req->set_param( 'content', $content );
			$req->set_param( 'object', [
				'type' => 'term',
				'ID'   => $term_id,
			] );
			$req->set_param( 'type', 'description' );
			$req->set_param( 'previousMetaByAI', $this->previous_meta( $skip_description, $orig['description'] ?? '' ) );
			$res = $ai->generate( $req );

			$status = $res['status'] ?? '';
			if ( $status === 'success' ) {
				$data['description'] = $res['message'];
				$this->push_entry( $entries, 'ok', 'term', $term->name, sprintf(
					/* translators: %s: generated description */
					__( 'Description: %s', 'slim-seo' ),
					$res['message']
				) );
			} else {
				++$stats['errors'];
				$this->push_entry( $entries, 'error', 'term', $term->name, sprintf(
					/* translators: %s: error message */
					__( 'Could not generate description: %s', 'slim-seo' ),
					$res['message'] ?? __( 'Unknown error', 'slim-seo' )
				) );
			}
		}

		$data = array_filter( $data );
		if ( empty( $data ) ) {
			delete_term_meta( $term_id, 'slim_seo' );
		} else {
			update_term_meta( $term_id, 'slim_seo', $data );
		}

		return [
			'entries' => $entries,
			'stats'   => $stats,
		];
	}

	/**
	 * Get the previous meta.
	 *
	 * @param bool   $skip_existing Whether to skip the existing meta.
	 * @param string $existing The existing meta.
	 * @return string The previous meta.
	 */
	private function previous_meta( bool $skip_existing, string $existing ): string {
		return $skip_existing ? '' : $existing;
	}

	/**
	 * Get the empty batch stats.
	 *
	 * @return array The empty batch stats.
	 */
	private function empty_batch_stats(): array {
		return [
			'success' => 0,
			'skipped' => 0,
			'errors'  => 0,
		];
	}

	/**
	 * Merge the batch stats.
	 *
	 * @param array $into The into array.
	 * @param array $add The add array.
	 */
	private function merge_batch_stats( array &$into, array $add ): void {
		foreach ( $add as $k => $v ) {
			if ( isset( $into[ $k ] ) ) {
				$into[ $k ] += (int) $v;
			}
		}
	}

	/**
	 * Push an entry to the entries array.
	 *
	 * @param array  $entries The entries array.
	 * @param string $level The level.
	 * @param string $scope The scope.
	 * @param string $ref The ref.
	 * @param string $message The message.
	 * @param array  $meta Optional. The meta array.
	 * @return void
	 */
	private function push_entry( array &$entries, string $level, string $scope, string $ref, string $message, array $meta = [] ): void {
		$row = [
			'time'    => current_time( 'H:i:s' ),
			'level'   => strtoupper( $level ),
			'scope'   => $scope,
			'ref'     => $ref,
			'message' => $message,
		];
		if ( ! empty( $meta ) ) {
			$row['meta'] = $meta;
		}
		$entries[] = $row;
	}
}
