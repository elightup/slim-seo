<?php
namespace SlimSEO;

use WP_Comment;
use WP_Post;
use WP_User;

class ImagesAlt {
	public function setup() {
		add_action( 'add_attachment', [ $this, 'generate_alt_text_on_upload' ] );

		// Fix missing alt text for avatar.
		add_filter( 'get_avatar', [ $this, 'add_avatar_alt' ], 10, 5 );
	}

	public function generate_alt_text_on_upload( int $post_id ): void {
		if ( ! wp_attachment_is_image( $post_id ) ) {
			return;
		}

		$title = get_post( $post_id )->post_title;

		// Sanitize the title: remove hyphens, underscores & extra spaces.
		$title = preg_replace( '%\s*[-_\s]+\s*%', ' ', $title );

		// Sanitize the title: capitalize first letter of every word (other letters lower case).
		$title = ucwords( strtolower( $title ) );

		// Set the image alt-text.
		update_post_meta( $post_id, '_wp_attachment_image_alt', $title );
	}

	public function add_avatar_alt( string $avatar, $id_or_email, int $size, string $default_value, string $alt ): string {
		if ( $alt ) {
			return $avatar;
		}

		$alt = $this->get_alt_from_id_or_email( $id_or_email );

		return str_replace( ' alt=\'\'', ' alt=\'' . esc_attr( $alt ) . '\'', $avatar );
	}

	/**
	 * Get proper alt text from id or email.
	 *
	 * @param  mixed $id_or_email User ID, Gravatar MD5 hash, user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @return string             User's display name if possible. Otherwise email's name (without the @domain.com).
	 */
	private function get_alt_from_id_or_email( $id_or_email ): string {
		if ( $id_or_email instanceof WP_Comment ) {
			return $id_or_email->comment_author;
		}

		$user  = null;
		$email = '';

		if ( $id_or_email instanceof WP_User ) {
			$user = $id_or_email;
		} elseif ( $id_or_email instanceof WP_Post ) {
			$user = get_user_by( 'id', (int) $id_or_email->post_author );
		} elseif ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', absint( $id_or_email ) );
		} elseif ( is_string( $id_or_email ) ) {
			$email = $id_or_email;
		}

		// Get the name from the email address.
		list ( $email ) = explode( '@', $email );

		return $user instanceof WP_User ? $user->display_name : $email;
	}
}
