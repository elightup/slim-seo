<?php
namespace SlimSEO\Sitemaps;

class Taxonomy {
	private $taxonomy;
	private $page;

	public function __construct( $taxonomy, $page = 1 ) {
		$this->taxonomy = $taxonomy;
		$this->page     = $page;
	}

	public static function get_query_args( $args = [] ) {
		return apply_filters( 'slim_seo_taxonomy_query_args', array_merge( [
			'hide_empty'             => true,
			'number'                 => 2000,
			'hierarchical'           => false,
			'update_term_meta_cache' => false,
		], $args ), $args );
	}

	public function output() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">', "\n";

		$offset     = ( $this->page - 1 ) * 2000;
		$query_args = self::get_query_args( [
			'taxonomy' => $this->taxonomy,
			'offset'   => $offset,
		] );
		$terms      = get_terms( $query_args );

		foreach ( $terms as $term ) {
			if ( ! $this->is_indexed( $term ) ) {
				continue;
			}

			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_term_link( $term, $this->taxonomy ) ), "</loc>\n";
			do_action( 'slim_seo_sitemap_term', $term );
			echo "\t</url>\n";
		}

		echo '</urlset>';
	}

	private function is_indexed( $term ) {
		$data = get_term_meta( $term->term_id, 'slim_seo', true );
		return empty( $data['noindex'] );
	}
}
