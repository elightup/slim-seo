<?php
namespace SlimSEO\Sitemaps;

use WP_User_Query;

class Index {
	private $post_types;
	private $taxonomies;

	public function __construct( array $post_types, array $taxonomies ) {
		$this->post_types = $post_types;
		$this->taxonomies = $taxonomies;
	}

	public function output() {
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";

		array_walk( $this->post_types, [ $this, 'output_post_type_sitemap' ] );
		array_walk( $this->taxonomies, [ $this, 'output_taxonomy_sitemap' ] );

		if ( User::is_active() ) {
			$this->output_user_sitemap();
		}

		echo '</sitemapindex>';
	}

	private function output_post_type_sitemap( $post_type ) {
		$query_args = PostType::get_query_args( [
			'post_type'     => $post_type,
			'no_found_rows' => false,
			'fields'        => 'ids',
		] );
		$query      = new \WP_Query( $query_args );
		if ( ! $query->post_count ) {
			return;
		}

		for ( $i = 1; $i <= $query->max_num_pages; $i++ ) {
			echo "\t<sitemap>\n";
			$index = 1 === $i ? '' : "-$i";
			echo "\t\t<loc>", esc_url( home_url( "sitemap-post-type-$post_type$index.xml" ) ), "</loc>\n";
			echo "\t</sitemap>\n";
		}
	}

	private function output_taxonomy_sitemap( $taxonomy ) {
		$term_count = wp_count_terms( $taxonomy, Taxonomy::get_query_args() );
		$max_page   = (int) ceil( $term_count / 2000 );
		for ( $i = 1; $i <= $max_page; $i++ ) {
			echo "\t<sitemap>\n";
			$index = 1 === $i ? '' : "-$i";
			echo "\t\t<loc>", esc_url( home_url( "sitemap-taxonomy-$taxonomy$index.xml" ) ), "</loc>\n";
			echo "\t</sitemap>\n";
		}
	}

	private function output_user_sitemap() {
		$query_args = User::get_query_args( [
			'field'       => 'ID',
			'count_total' => true,
		] );
		$query      = new WP_User_Query( $query_args );
		$total      = $query->get_total();
		$max_page   = (int) ceil( $total / 2000 );
		for ( $i = 1; $i <= $max_page; $i++ ) {
			echo "\t<sitemap>\n";
			$index = 1 === $i ? '' : "-$i";
			echo "\t\t<loc>", esc_url( home_url( "sitemap-user$index.xml" ) ), "</loc>\n";
			echo "\t</sitemap>\n";
		}
	}
}
