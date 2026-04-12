<?php
namespace SlimSEO\MetaTags;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WP_Query;
use WP_Term;

class BulkAI {
	private const REST_NS   = 'slim-seo';
	private const BATCH     = 3;
	private const MAX_BATCH = 10;

	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ], 10 );
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
				'batch_size'       => [
					'type'    => 'integer',
					'default' => self::BATCH,
					'minimum' => 1,
					'maximum' => self::MAX_BATCH,
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
	 * Process a single chunk of bulk AI generation.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function process_chunk( WP_REST_Request $request ) {
		$t0 = microtime( true );

		$batch  = min( self::MAX_BATCH, max( 1, (int) $request->get_param( 'batch_size' ) ) );
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

		$entries     = [];
		$batch_stats = $this->empty_batch_stats();
		$next_phase  = $phase;
		$next_offset = $offset;
		$done        = true;

		$this->push_entry(
			$entries,
			'info',
			'batch',
			'System',
			sprintf(
				/* translators: %d: batch size */
				__( 'Processing next %d items...', 'slim-seo' ),
				$batch
			)
		);

		if ( 'posts' === $phase && ! empty( $post_types ) ) {
			$result = $this->process_posts_phase( $post_types, $batch, $offset, $skip_title, $skip_description, $entries, $batch_stats );

			$entries     = $result['entries'];
			$batch_stats = $result['batch_stats'];

			if ( $result['has_more'] ) {
				$next_offset = $offset + $result['count'];
				$next_phase  = 'posts';
				$done        = false;
			} elseif ( ! empty( $taxonomies ) ) {
				$this->push_entry( $entries, 'info', 'batch', 'System', __( 'All posts done. Now processing taxonomies...', 'slim-seo' ) );
				$next_phase  = 'terms';
				$next_offset = 0;
				$done        = false;
			}
		} elseif ( 'terms' === $phase && ! empty( $taxonomies ) ) {
			$result = $this->process_terms_phase( $taxonomies, $batch, $offset, $skip_title, $skip_description, $entries, $batch_stats );

			$entries     = $result['entries'];
			$batch_stats = $result['batch_stats'];

			if ( $result['has_more'] ) {
				$next_offset = $offset + $result['count'];
				$next_phase  = 'terms';
				$done        = false;
			}
		} elseif ( 'posts' === $phase && empty( $post_types ) && ! empty( $taxonomies ) ) {
			$this->push_entry( $entries, 'info', 'batch', 'System', __( 'Processing taxonomies...', 'slim-seo' ) );
			$next_phase  = 'terms';
			$next_offset = 0;
			$done        = false;
		}

		$elapsed_ms = (int) round( ( microtime( true ) - $t0 ) * 1000 );

		$this->push_entry(
			$entries,
			'info',
			'batch',
			'System',
			sprintf(
				/* translators: 1: items generated, 2: items skipped */
				__( 'Batch complete. Generated: %1$d, skipped: %2$d.', 'slim-seo' ),
				$batch_stats['ai_calls'],
				$batch_stats['skipped_steps']
			)
		);

		return new WP_REST_Response( [
			'done'        => $done,
			'next_phase'  => $next_phase,
			'next_offset' => $next_offset,
			'elapsed_ms'  => $elapsed_ms,
			'batch_stats' => $batch_stats,
			'log_entries' => $entries,
		] );
	}

	private function process_posts_phase( array $post_types, int $batch, int $offset, bool $skip_title, bool $skip_description, array $entries, array $batch_stats ): array {
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

	private function process_terms_phase( array $taxonomies, int $batch, int $offset, bool $skip_title, bool $skip_description, array $entries, array $batch_stats ): array {
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

	private function process_post( int $post_id, bool $skip_title, bool $skip_description ): array {
		$entries = [];
		$stats   = $this->empty_batch_stats();

		$post = get_post( $post_id );
		if ( ! $post ) {
			++$stats['errors'];
			$this->push_entry( $entries, 'error', 'post', (string) $post_id, __( 'Post not found, skipping.', 'slim-seo' ) );
			return [
				'entries' => $entries,
				'stats' => $stats,
			];
		}

		$stats['items_touched'] = 1;
		$title = get_the_title( $post );
		$data  = get_post_meta( $post_id, 'slim_seo', true );
		$data  = is_array( $data ) ? $data : [];
		$orig  = $data;

		$ai = new AI();

		// Generate title.
		if ( $skip_title && ! empty( $orig['title'] ) ) {
			++$stats['skipped_steps'];
			$this->push_entry( $entries, 'skip', 'post', $title, __( 'Title already exists, skipped.', 'slim-seo' ) );
		} else {
			$req = new WP_REST_Request( 'POST' );
			$req->set_param( 'title', $title );
			$req->set_param( 'content', '' );
			$req->set_param( 'object', [
				'type' => 'post',
				'ID' => $post_id,
			] );
			$req->set_param( 'type', 'title' );
			$req->set_param( 'previousMetaByAI', $this->previous_meta( $skip_title, $orig['title'] ?? '' ) );
			$res = $ai->generate( $req );
			++$stats['ai_calls'];

			if ( ( $res['status'] ?? '' ) !== 'success' ) {
				++$stats['errors'];
				$this->push_entry( $entries, 'error', 'post', $title, sprintf(
					/* translators: %s: error message */
					__( 'Could not generate title: %s', 'slim-seo' ),
					$res['message'] ?? __( 'Unknown error', 'slim-seo' )
				) );
			} else {
				$data['title'] = $res['message'];
				$this->push_entry( $entries, 'ok', 'post', $title, sprintf(
					/* translators: %s: generated title */
					__( 'Title: %s', 'slim-seo' ),
					$res['message']
				) );
			}
		}

		// Generate description.
		if ( $skip_description && ! empty( $orig['description'] ) ) {
			++$stats['skipped_steps'];
			$this->push_entry( $entries, 'skip', 'post', $title, __( 'Description already exists, skipped.', 'slim-seo' ) );
		} else {
			$req = new WP_REST_Request( 'POST' );
			$req->set_param( 'title', '' );
			$req->set_param( 'content', '' );
			$req->set_param( 'object', [
				'type' => 'post',
				'ID' => $post_id,
			] );
			$req->set_param( 'type', 'description' );
			$req->set_param( 'previousMetaByAI', $this->previous_meta( $skip_description, $orig['description'] ?? '' ) );
			$res = $ai->generate( $req );
			++$stats['ai_calls'];

			if ( ( $res['status'] ?? '' ) !== 'success' ) {
				++$stats['errors'];
				$this->push_entry( $entries, 'error', 'post', $title, sprintf(
					/* translators: %s: error message */
					__( 'Could not generate description: %s', 'slim-seo' ),
					$res['message'] ?? __( 'Unknown error', 'slim-seo' )
				) );
			} else {
				$data['description'] = $res['message'];
				$this->push_entry( $entries, 'ok', 'post', $title, sprintf(
					/* translators: %s: generated description */
					__( 'Description: %s', 'slim-seo' ),
					$res['message']
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
			'stats' => $stats,
		];
	}

	private function process_term( WP_Term $term, bool $skip_title, bool $skip_description ): array {
		$entries = [];
		$stats   = $this->empty_batch_stats();
		$term_id = (int) $term->term_id;

		$stats['items_touched'] = 1;

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
			++$stats['skipped_steps'];
			$this->push_entry( $entries, 'skip', 'term', $term->name, __( 'Title already exists, skipped.', 'slim-seo' ) );
		} else {
			$req = new WP_REST_Request( 'POST' );
			$req->set_param( 'title', $term->name );
			$req->set_param( 'content', $content );
			$req->set_param( 'object', [
				'type' => 'term',
				'ID' => $term_id,
			] );
			$req->set_param( 'type', 'title' );
			$req->set_param( 'previousMetaByAI', $this->previous_meta( $skip_title, $orig['title'] ?? '' ) );
			$res = $ai->generate( $req );
			++$stats['ai_calls'];

			if ( ( $res['status'] ?? '' ) !== 'success' ) {
				++$stats['errors'];
				$this->push_entry( $entries, 'error', 'term', $term->name, sprintf(
					/* translators: %s: error message */
					__( 'Could not generate title: %s', 'slim-seo' ),
					$res['message'] ?? __( 'Unknown error', 'slim-seo' )
				) );
			} else {
				$data['title'] = $res['message'];
				$this->push_entry( $entries, 'ok', 'term', $term->name, sprintf(
					/* translators: %s: generated title */
					__( 'Title: %s', 'slim-seo' ),
					$res['message']
				) );
			}
		}

		// Generate description.
		if ( $skip_description && ! empty( $orig['description'] ) ) {
			++$stats['skipped_steps'];
			$this->push_entry( $entries, 'skip', 'term', $term->name, __( 'Description already exists, skipped.', 'slim-seo' ) );
		} else {
			$req = new WP_REST_Request( 'POST' );
			$req->set_param( 'title', '' );
			$req->set_param( 'content', $content );
			$req->set_param( 'object', [
				'type' => 'term',
				'ID' => $term_id,
			] );
			$req->set_param( 'type', 'description' );
			$req->set_param( 'previousMetaByAI', $this->previous_meta( $skip_description, $orig['description'] ?? '' ) );
			$res = $ai->generate( $req );
			++$stats['ai_calls'];

			if ( ( $res['status'] ?? '' ) !== 'success' ) {
				++$stats['errors'];
				$this->push_entry( $entries, 'error', 'term', $term->name, sprintf(
					/* translators: %s: error message */
					__( 'Could not generate description: %s', 'slim-seo' ),
					$res['message'] ?? __( 'Unknown error', 'slim-seo' )
				) );
			} else {
				$data['description'] = $res['message'];
				$this->push_entry( $entries, 'ok', 'term', $term->name, sprintf(
					/* translators: %s: generated description */
					__( 'Description: %s', 'slim-seo' ),
					$res['message']
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
			'stats' => $stats,
		];
	}

	private function previous_meta( bool $skip_existing, string $existing ): string {
		return $skip_existing ? '' : $existing;
	}

	private function empty_batch_stats(): array {
		return [
			'ai_calls'      => 0,
			'skipped_steps' => 0,
			'errors'        => 0,
			'items_touched' => 0,
		];
	}

	private function merge_batch_stats( array &$into, array $add ): void {
		foreach ( $add as $k => $v ) {
			if ( isset( $into[ $k ] ) ) {
				$into[ $k ] += (int) $v;
			}
		}
	}

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
