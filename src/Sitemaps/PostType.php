<?php
namespace SlimSEO\Sitemaps;

use SlimSEO\Helpers\Data;
use SlimSEO\Helpers\Images;
use WP_Post;

class PostType {
	private $post_type;
	private $page;
	private $doc;

	public function __construct( string $post_type, int $page = 1 ) {
		$this->post_type = $post_type;
		$this->page      = $page;
	}

	public static function get_query_args( array $args = [] ): array {
		return apply_filters( 'slim_seo_sitemap_post_type_query_args', array_merge( [
			'post_status'            => 'publish',
			'has_password'           => false,

			'ignore_sticky_posts'    => true,

			'no_found_rows'          => true,
			'update_post_term_cache' => false,

			'order'                  => 'DESC',
			'orderyby'               => 'date',

			// Set 1000 to compatible with News sitemap structure.
			'posts_per_page'         => 1000, // @codingStandardsIgnoreLine.
		], $args ), $args );
	}

	public function output(): void {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">', "\n";

		if ( $this->page === 1 ) {
			$this->output_homepage();
			$this->output_post_type_archive();
		}

		$query_args = self::get_query_args( [
			'post_type' => $this->post_type,
			'paged'     => $this->page,
		] );
		$query      = new \WP_Query( $query_args );

		$post_images = [];
		$image_ids   = [];

		// Cache images by IDs.
		foreach ( $query->posts as $post ) {
			if ( ! $this->is_indexed( $post ) ) {
				continue;
			}

			$images                   = Images::get_post_images( $post );
			$post_images[ $post->ID ] = $images;
			$images                   = array_filter( $images, function ( $image ) {
				return is_numeric( $image );
			} );
			$image_ids                = array_merge( $image_ids, $images );
		}
		$this->cache_images( $image_ids );

		foreach ( $query->posts as $post ) {
			if ( ! $this->is_indexed( $post ) ) {
				continue;
			}

			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_permalink( $post ) ), "</loc>\n";
			echo "\t\t<lastmod>", esc_html( gmdate( 'c', strtotime( $post->post_modified_gmt ) ) ), "</lastmod>\n";

			// News sitemap for posts published within 2 days.
			if ( 'post' === $this->post_type && $this->is_published_within_2days( $post ) ) {
				$this->output_news( $post );
			}

			// Output post images to create image sitemap. Doesn't generate any queries because images are cached.
			$images = $post_images[ $post->ID ];
			foreach ( $images as &$image ) {
				$image = is_string( $image ) ? $image : wp_get_attachment_url( $image );
				if ( $this->is_internal( $image ) ) {
					$this->output_image( $image );
				}
			}

			do_action( 'slim_seo_sitemap_post', $post );
			echo "\t</url>\n";
		}

		echo '</urlset>';
	}

	private function output_homepage(): void {
		if ( 'page' !== $this->post_type || 'posts' !== get_option( 'show_on_front' ) ) {
			return;
		}
		echo "\t<url>\n";
		echo "\t\t<loc>", esc_url( home_url( '/' ) ), "</loc>\n";
		echo "\t</url>\n";
	}

	private function output_post_type_archive(): void {
		// Ignore post as it's always the homepage or blog page, which are already included in the "page" sitemap.
		if ( $this->post_type === 'post' ) {
			return;
		}

		// If post type archive is a page, ignore it because it's already included in the "page" sitemap.
		$archive_page = Data::get_post_type_archive_page( $this->post_type );
		if ( $archive_page ) {
			return;
		}

		$url = get_post_type_archive_link( $this->post_type );
		if ( ! $url ) {
			return;
		}
		echo "\t<url>\n";
		echo "\t\t<loc>", esc_url( $url ), "</loc>\n";
		echo "\t</url>\n";
	}

	private function output_image( string $url ): void {
		echo "\t\t<image:image>\n";
		echo "\t\t\t<image:loc>", esc_url( $url ), "</image:loc>\n";
		echo "\t\t</image:image>\n";
	}

	private function output_news( WP_Post $post ): void {
		echo "\t\t<news:news>\n";
		echo "\t\t\t<news:publication>\n";
		echo "\t\t\t\t<news:name>", esc_html( get_bloginfo( 'name' ) ), "</news:name>\n";
		echo "\t\t\t\t<news:language>", esc_html( $this->get_site_language() ), "</news:language>\n";
		echo "\t\t\t</news:publication>\n";
		echo "\t\t\t<news:publication_date>", esc_html( gmdate( 'c', strtotime( $post->post_date_gmt ) ) ), "</news:publication_date>\n";
		echo "\t\t\t<news:title>", esc_html( $post->post_title ), "</news:title>\n";
		echo "\t\t</news:news>\n";
	}

	private function is_internal( string $url ): bool {
		$home_url = untrailingslashit( home_url() );
		return str_contains( $url, $home_url );
	}

	private function is_indexed( WP_Post $post ): bool {
		$data = get_post_meta( $post->ID, 'slim_seo', true );
		return empty( $data['noindex'] );
	}

	private function cache_images( array $image_ids ): void {
		update_meta_cache( 'post', $image_ids );
		$this->cache_posts( $image_ids );
	}

	private function cache_posts( array $post_ids ): void {
		if ( empty( $post_ids ) ) {
			return;
		}

		$post_ids = implode( ',', $post_ids );

		global $wpdb;
		$sql = "SELECT * FROM $wpdb->posts WHERE ID IN ($post_ids)";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$posts = $wpdb->get_results( $sql );

		foreach ( $posts as $post ) {
			$post = sanitize_post( $post, 'raw' );
			wp_cache_add( $post->ID, $post, 'posts' );
		}
	}

	private function is_published_within_2days( WP_Post $post ): bool {
		$timestamp             = strtotime( $post->post_date_gmt );
		$two_days_ago_midnight = strtotime( '-2 days midnight', strtotime( gmdate( 'Y-m-d' ) ) );

		return $timestamp >= $two_days_ago_midnight;
	}

	private function get_site_language(): string {
		$locale = get_locale();
		$locale = str_replace( [ '-', '_' ], '-', strtolower( $locale ) );

		// Exception: For Simplified Chinese, use zh-cn and for Traditional Chinese, use zh-tw.
		if ( in_array( $locale, [ 'zh-cn', 'zh-tw' ], true ) ) {
			return $locale;
		}

		return explode( '-', $locale )[0];
	}
}
