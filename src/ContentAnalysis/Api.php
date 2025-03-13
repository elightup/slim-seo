<?php
namespace SlimSEO\ContentAnalysis;

use WP_REST_Server;
use WP_REST_Request;

class Api {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		register_rest_route( 'slim-seo-content-analysis', 'image_detail', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_image_detail' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function get_image_detail( WP_REST_Request $request ): array {
		$id  = $request->get_param( 'id' );
		$src = $request->get_param( 'src' );

		if ( empty( $id ) ) {
			global $wpdb;

			$upload_dir = wp_upload_dir();

			if ( false !== strpos( $src, $upload_dir['baseurl'] ) ) {
				$image_path = str_replace( $upload_dir['baseurl'] . '/', '', $src );
				$image_path = preg_replace( '/-\d+x\d+(?=\.[^.\s]{3,4}$)/', '', $image_path );

				$attachment_id = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT `ID` FROM {$wpdb->posts}
						WHERE `post_type` = 'attachment'
						AND `guid` LIKE %s",
						'%' . $wpdb->esc_like( $image_path ) . '%'
					)
				);

				if ( ! empty( $attachment_id ) ) {
					$id = $attachment_id;
				}
			}

			if ( empty( $id ) ) {
				return [];
			}
		}

		$metadata = wp_get_attachment_metadata( $id );

		if ( empty( $metadata['file'] ) ) {
			return [];
		}

		$file_path = get_attached_file( $id );
		$file_name = basename( $file_path );

		$image_detail = [
			'width'    => $metadata['width'],
			'height'   => $metadata['height'],
			'size'     => round( (int) $metadata['filesize'] / 1024 ),
			'filename' => $file_name,
		];

		return $image_detail;
	}

	public function has_permission(): bool {
		return current_user_can( 'manage_options' );
	}
}
