<?php
namespace SlimSEO\Integrations;

use WP_Post;

class FileRenamingOnUpload {
	private $alt       = '';
	private $separator = '-';
	
	public function is_active(): bool {
		return function_exists( '\FROU\file_renaming_on_upload_autoload' );
	}

	public function setup(): void {
		add_action( 'add_attachment', [ $this, 'generate_alt_text_on_upload' ] );
		add_filter( 'frou_sanitize_file_name', array( $this, 'sanitize_filename' ), 10 );
		add_filter( 'frou_after_sanitize_file_name', array( $this, 'after_sanitize_filename' ), 10 );
	}

	public function generate_alt_text_on_upload( int $post_id ): void {
		if ( ! wp_attachment_is_image( $post_id ) ) {
			return;
		}

		$alt = $this->normalize( $this->alt );
		update_post_meta( $post_id, '_wp_attachment_image_alt', $alt );
	}

	public function sanitize_filename( array $filename_infs ): array {
		$this->separator = $filename_infs['structure']['separator'];
		return $filename_infs;
	}

	public function after_sanitize_filename( string $filename ): string {
		$this->alt = str_replace( $this->separator, ' ', $filename );
		return $filename;
	}

	private function normalize( string $alt ): string {
		// Remove hyphens, underscores & extra spaces.
		$alt = preg_replace( '%\s*[-_\s]+\s*%', ' ', $alt );

		// Capitalize first letter of every word (other letters lower case).
		$alt = ucwords( strtolower( $alt ) );

		return $alt;
	}
}
