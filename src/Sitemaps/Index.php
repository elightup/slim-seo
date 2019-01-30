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
				'post_type'      => $post_type,
				'posts_per_page' => 1,
			]
		);
		$query      = new \WP_Query( $query_args );
		if ( ! $query->post_count ) {
			return;
		}

		echo "\t<sitemap>\n";
		echo "\t\t<loc>", esc_url( home_url( "sitemap-post-type-$post_type.xml" ) ), "</loc>\n";

		$last_modified = date( 'c', strtotime( $query->posts[0]->post_modified_gmt ) );

		echo "\t\t<lastmod>", esc_html( $last_modified ), "</lastmod>\n";
		echo "\t</sitemap>\n";
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
				'taxonomy' => $taxonomy,
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
