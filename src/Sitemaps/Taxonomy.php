<?php
namespace SlimSEO\Sitemaps;

class Taxonomy {
	private $taxonomy;

	public static $query_args = [
		'hide_empty' => true,
		'number'     => 500, // Maximum number of links in a sitemap. See https://support.google.com/webmasters/answer/75712
	];

	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}

	public function output() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";

		$query_args = array_merge(
			self::$query_args,
			[
				'taxonomy' => $this->taxonomy,
			]
		);
		$terms      = get_terms( $query_args );

		foreach ( $terms as $term ) {
			if ( ! $this->is_indexed( $term ) ) {
				continue;
			}

			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_term_link( $term, $this->taxonomy ) ), "</loc>\n";
			echo "\t</url>\n";
		}

		echo '</urlset>';
	}

	private function is_indexed( $term ) {
		$data = get_term_meta( $term->term_id, 'slim_seo', true );
		return empty( $data['noindex'] );
	}
}
