<?php
namespace SlimSEO\MetaTags;

class LinkedIn {
	public function setup(): void {
		add_action( 'wp_head', [ $this, 'output' ] );
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

		$author = $this->get_author();
		$author = apply_filters( 'slim_seo_linkedin_author', $author );
		if ( $author ) {
			echo '<meta name="author" content="' . esc_attr( $author ) . '">', "\n";
		}

		$date = $this->get_date();
		$date = apply_filters( 'slim_seo_linkedin_date', $date );
		if ( $date ) {
			echo '<meta name="date" content="' . esc_attr( $date ) . '">', "\n";
		}
	}

	private function get_author(): string {
		return get_the_author();
	}

	private function get_date(): string {
		return ( string ) gmdate( 'c', strtotime( get_queried_object()->post_date_gmt ) );
	}
}