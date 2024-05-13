<?php
namespace SlimSEO\Sitemaps;

use SlimSEO\Helpers\Data;
use WP_Post;

class News {
	private $page;

	public function __construct( int $page = 1 ) {
		$this->page = $page;
	}

	public static function get_query_args( array $args = [] ): array {
		return apply_filters( 'slim_seo_sitemap_news_query_args', array_merge( [
			'post_status'            => 'publish',
			'has_password'           => false,

			'ignore_sticky_posts'    => true,

			'no_found_rows'          => true,
			'date_query' => array(
				array(
					'after'     => 'midnight 2 days ago',
					'inclusive' => true,
					),
			),

			'order'                  => 'DESC',
			'orderyby'               => 'date',

			'posts_per_page'         => 2000, // @codingStandardsIgnoreLine.
		], $args ), $args );
	}

	public function output(): void {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">', "\n";

		$query_args = self::get_query_args( [
			'paged' => $this->page,
		] );
		$query      = new \WP_Query( $query_args );

		foreach ( $query->posts as $post ) {
			if ( ! $this->is_indexed( $post ) ) {
				continue;
			}

			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_permalink( $post ) ), "</loc>\n";
			// echo "\t\t<lastmod>", esc_html( gmdate( 'c', strtotime( $post->post_modified_gmt ) ) ), "</lastmod>\n";
			echo "\t\t<news:news>\n";
			echo "\t\t\t<news:publication>\n";
			echo "\t\t\t\t<news:name>", esc_html( the_author_meta( 'user_nicename' , $post->post_author ) ),"</news:name>\n";
			echo "\t\t\t\t<news:language>", get_locale() ,"</news:language>\n";
			echo "\t\t\t</news:publication>\n";
			echo "\t\t\t<news:publication_date>", esc_html( gmdate( 'Y-m-d', strtotime( $post->post_date_gmt ) ) ),"</news:publication_date>\n";
			echo "\t\t\t<news:title>", $post->post_title ,"</news:title>\n";
			echo "\t\t</news:news>\n";

			do_action( 'slim_seo_sitemap_post', $post );
			echo "\t</url>\n";
		}

		echo '</urlset>';
	}

	private function is_indexed( WP_Post $post ): bool {
		$data = get_post_meta( $post->ID, 'slim_seo', true );
		return empty( $data['noindex'] );
	}
}
