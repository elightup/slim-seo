<?php
namespace SlimSEO\Sitemaps;

use SlimSEO\Helpers\Data;
use WP_Post;

class PostType {
	private $post_type;
	private $page;

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
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,

			'order'                  => 'DESC',
			'orderyby'               => 'date',

			'posts_per_page'         => 2000, // @codingStandardsIgnoreLine.
		], $args ), $args );
	}

	public function output(): void {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml">', "\n";

		if ( $this->page === 1 ) {
			$this->output_homepage();
			$this->output_post_type_archive();
		}

		$query_args = self::get_query_args( [
			'post_type' => $this->post_type,
			'paged'     => $this->page,
		] );
		$query      = new \WP_Query( $query_args );

		foreach ( $query->posts as $post ) {
			if ( ! $this->is_indexed( $post ) ) {
				continue;
			}

			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_permalink( $post ) ), "</loc>\n";
			echo "\t\t<lastmod>", esc_html( gmdate( 'c', strtotime( $post->post_modified_gmt ) ) ), "</lastmod>\n";

			$images = $this->get_post_images( $post );
			$images = array_map( [ $this, 'normalize_image' ], $images );
			$images = array_filter( $images );
			$images = array_filter( $images, [ $this, 'is_internal' ] );
			array_walk( $images, [ $this, 'output_image' ] );

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

	private function normalize_image( $image ): string {
		// If we get image ID only.
		if ( is_numeric( $image ) ) {
			return get_attached_file( $image ) ? wp_get_attachment_image_url( $image, 'full' ) : '';
		}

		return $this->get_absolute_url( $image );
	}

	private function get_post_images( WP_Post $post ): array {
		$images = [];

		// Post thumbnail.
		$images[] = get_post_thumbnail_id( $post );

		// Get images from post content.
		$images = array_merge( $images, $this->get_images_from_html( $post->post_content ) );

		return array_filter( $images );
	}

	private function get_images_from_html( string $html ): array {
		// Use DOMDocument instead of SimpleXML to load non-well-formed HTML.
		if ( ! class_exists( 'DOMDocument' ) ) {
			return [];
		}

		// Set encoding.
		$html = '<?xml encoding="' . get_bloginfo( 'charset' ) . '"?>' . $html;

		// Do not generate a notice when there's an error.
		libxml_use_internal_errors( true );

		$doc = new \DOMDocument( $html );
		$doc->loadHTML( $html );

		// Clear the errors to clean up the memory.
		libxml_clear_errors();

		$values = [];
		$images = $doc->getElementsByTagName( 'img' );
		foreach ( $images as $image ) {
			$src = $image->getAttribute( 'src' );
			if ( empty( $src ) ) {
				continue;
			}

			$class = $image->getAttribute( 'class' );

			// Uploaded images.
			if ( preg_match( '/wp-image-(\d+)/', $class, $matches ) ) {
				$values[] = (int) $matches[1];
				continue;
			}

			$values[] = $src;
		}

		return $values;
	}

	private function is_internal( string $url ): bool {
		$home_url = untrailingslashit( home_url() );
		return str_contains( $url, $home_url );
	}

	private function get_absolute_url( string $url ): string {
		if ( wp_parse_url( $url, PHP_URL_SCHEME ) ) {
			return $url;
		}

		$url_parts = wp_parse_url( home_url() );

		// Non-protocol URL.
		if ( str_starts_with( $url, '//' ) ) {
			return "{$url_parts['scheme']}:{$url}";
		}

		// Relative URL.
		return $url_parts['scheme'] . '://' . trailingslashit( $url_parts['host'] ) . ltrim( $url, '/' );
	}

	private function is_indexed( WP_Post $post ): bool {
		$data = get_post_meta( $post->ID, 'slim_seo', true );
		return empty( $data['noindex'] );
	}
}
