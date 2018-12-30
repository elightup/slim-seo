<?php
namespace SlimSEO\Sitemaps;

class Taxonomy {
	private $taxonomy;

	public static $query_args = [
		'hide_empty'             => true,
		'fields'                 => 'ids',
		'update_term_meta_cache' => false,
	];

	/**
	 * Maximum number of links in sitemap Google recommends 500.
	 *
	 * @link https://support.google.com/webmasters/answer/75712
	 * @var integer
	 */
	private $limit = 500;

	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}

	public function output() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";

		$query_args = array_merge(
			self::$query_args,
			[
				'taxonomy' => $this->taxonomy,
				'number'   => $this->limit,
			]
		);
		$terms      = get_terms( $query_args );

		foreach ( $terms as $term ) {
			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_term_link( $term, $this->taxonomy ) ), "</loc>\n";
			echo "\t</url>\n";
		}

		echo '</urlset>';
	}
}
