<?php
namespace SlimSEO\Redirection;

use WP_REST_Server;
use WP_REST_Request;
use SlimSEO\Redirection\Api\Base;
use SlimSEO\Redirection\Database\Redirects as DbRedirects;

class ExportImport extends Base {
	protected $db_redirects;

	public function __construct( DbRedirects $db_redirects ) {
		parent::__construct();
		$this->db_redirects = $db_redirects;
	}

	public function register_routes() {
		register_rest_route( 'slim-seo-redirection', 'export', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'export' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );

		register_rest_route( 'slim-seo-redirection', 'import', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'import' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function export() {
		$redirects = $this->db_redirects->list();

		if ( empty( $redirects ) ) {
			return false;
		}

		$file_name = 'slimseo-redirects.csv';
		$data      = [];
		$header    = [
			esc_html( __( 'Type', 'slim-seo' ) ),
			esc_html( __( 'Condition', 'slim-seo' ) ),
			esc_html( __( 'From', 'slim-seo' ) ),
			esc_html( __( 'To', 'slim-seo' ) ),
			esc_html( __( 'Note', 'slim-seo' ) ),
			esc_html( __( 'Enable', 'slim-seo' ) ),
			esc_html( __( 'Ignore Parameters', 'slim-seo' ) ),
		];

		foreach ( $redirects as $redirect ) {
			$data[] = [
				$redirect['type'],
				$redirect['condition'],
				$redirect['from'],
				$redirect['to'],
				$redirect['note'],
				$redirect['enable'],
				$redirect['ignoreParameters'],
			];
		}

		$data = array_merge( [ $header ], $data );

		return [
			'filename' => $file_name,
			'data'     => $data,
		];
	}

	public function import( WP_REST_Request $request ): bool {
		$text = $request->get_param( 'text' );
		$text = wp_unslash( $text );
		$rows = preg_split( '/\r\n|\r|\n/', $text );

		if ( empty( $rows ) ) {
			return false;
		}

		$header = str_getcsv( $rows[0] );

		/**
		 * Return if
		 *  1. $header is empty
		 *  2. $header is not array
		 *  3. $header does not have 7 fields
		 */
		if ( empty( $header ) || ! is_array( $header ) || 7 !== count( $header ) ) {
			return false;
		}

		$redirects        = $this->db_redirects->list();
		$redirects_amount = count( $rows );
		$added            = false;

		for ( $i = 1; $i < $redirects_amount; $i++ ) {
			$redirect = array_combine( [
				'type',
				'condition',
				'from',
				'to',
				'note',
				'enable',
				'ignoreParameters',
			], str_getcsv( $rows[ $i ] ) );

			if ( $this->db_redirects->exists( $redirect['from'] ) ) {
				continue;
			}

			$added = true;

			$redirect['from'] = Helper::normalize_url( $redirect['from'], false );
			$redirect['to']   = Helper::normalize_url( $redirect['to'], true, true, false );
			$redirect['note'] = sanitize_text_field( $redirect['note'] );

			$redirects[ uniqid() ] = $redirect;
		}

		if ( ! $added ) {
			return true;
		}

		$this->db_redirects->update_all( $redirects );

		return true;
	}
}
