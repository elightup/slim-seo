<?php
namespace SlimSEO\Sitemaps;

class User {
	private $page;

	public function __construct( int $page = 1 ) {
		$this->page = $page;
	}

	public static function is_active() {
		return apply_filters( 'slim_seo_user_sitemap', false );
	}

	public static function get_query_args( array $args = [] ): array {
		return apply_filters( 'slim_seo_user_query_args', array_merge( [
			'number'      => 2000,
			'count_total' => false,
		], $args ), $args );
	}

	public function output() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">', "\n";

		$query_args = self::get_query_args( [
			'paged' => $this->page,
		] );
		$users      = get_users( $query_args );

		foreach ( $users as $user ) {
			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_author_posts_url( $user->ID ) ), "</loc>\n";
			do_action( 'slim_seo_sitemap_user', $user );
			echo "\t</url>\n";
		}

		echo '</urlset>';
	}
}
