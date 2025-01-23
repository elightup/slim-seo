<?php
namespace SlimSEO\MetaTags;

class LinkedIn {
	public function setup(): void {
		add_action( 'slim_seo_head', [ $this, 'output' ] );
	}

	/**
	 *  How to specify information for social media sharing.
	 *
	 * @link https://www.linkedin.com/advice/3/how-do-you-specify-images-media-social-sharing-html-skills-html
	 */
	public function output(): void {
		// Generate meta tags author and date for singular pages only.
		if ( ! is_singular() ) {
			return;
		}

		$author = get_the_author_meta( 'display_name', get_queried_object()->post_author );
		$author = apply_filters( 'slim_seo_linkedin_author', $author, get_queried_object_id() );
		if ( $author ) {
			echo '<meta name="author" content="' . esc_attr( $author ) . '">', "\n";
		}

		$date = wp_date( 'c', strtotime( get_queried_object()->post_date_gmt ) );
		$date = apply_filters( 'slim_seo_linkedin_date', $date, get_queried_object_id() );
		if ( $date ) {
			echo '<meta name="date" content="' . esc_attr( $date ) . '">', "\n";
		}
	}
}
