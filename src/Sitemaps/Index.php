<?php
namespace SlimSEO\Sitemaps;

class Index {
	public function output() {
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";

		$this->output_post_type_sitemaps();
		$this->output_taxonomy_sitemaps();

		echo '</sitemapindex>';
	}

	private function output_post_type_sitemaps() {
		$post_types = get_post_types( [ 'public' => true ] );
		array_walk( $post_types, [ $this, 'output_post_type_sitemap' ] );
	}

	private function output_post_type_sitemap( $post_type ) {
		$query_args = array_merge(
			PostType::$query_args,
			[
				'post_type'              => $post_type,
				'no_found_rows'          => false,
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
			]
		);
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

	private function output_taxonomy_sitemaps() {
		$taxonomies = get_taxonomies(
			[
				'public'  => true,
				'show_ui' => true,
			]
		);
		array_walk( $taxonomies, [ $this, 'output_taxonomy_sitemap' ] );
	}

	private function output_taxonomy_sitemap( $taxonomy ) {
		$query_args = array_merge(
			Taxonomy::$query_args,
			[
				'taxonomy'               => $taxonomy,
				'fields'                 => 'ids',
				'update_term_meta_cache' => false,
			]
		);
		$terms      = get_terms( $query_args );
		if ( empty( $terms ) ) {
			return;
		}

		echo "\t<sitemap>\n";
		echo "\t\t<loc>", esc_url( home_url( "sitemap-taxonomy-$taxonomy.xml" ) ), "</loc>\n";
		echo "\t</sitemap>\n";
	}
}
