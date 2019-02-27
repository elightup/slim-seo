<?php
namespace SlimSEO\Sitemaps;

class PostType {
	private $post_type;

	public static $query_args = [
		'post_status'            => 'publish',
		'has_password'           => false,

		'ignore_sticky_posts'    => true,

		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,

		'order'                  => 'DESC',
		'orderyby'               => 'date',

		'posts_per_page'         => 500, // Maximum number of links in a sitemap. See https://support.google.com/webmasters/answer/75712
	];

	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	public function output() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";

		$this->output_homepage();

		$query_args = array_merge(
			self::$query_args,
			[
				'post_type' => $this->post_type,
			]
		);
		$query      = new \WP_Query( $query_args );

		foreach ( $query->posts as $post ) {
			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_permalink( $post ) ), "</loc>\n";
			echo "\t\t<lastmod>", esc_html( date( 'c', strtotime( $post->post_modified_gmt ) ) ), "</lastmod>\n";
			echo "\t</url>\n";
		}

		echo '</urlset>';
	}

	private function output_homepage() {
		if ( 'page' !== $this->post_type || 'posts' !== get_option( 'show_on_front' ) ) {
			return;
		}
		echo "\t<url>\n";
		echo "\t\t<loc>", esc_url( home_url() ), "</loc>\n";
		echo "\t</url>\n";
	}
}
