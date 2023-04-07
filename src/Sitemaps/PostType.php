<?php
namespace SlimSEO\Sitemaps;

class PostType {
	private $post_type;
	private $page;
	private $current_post;

	public function __construct( $post_type, $page = 1 ) {
		$this->post_type = $post_type;
		$this->page      = $page;
	}

	public static function get_query_args( $args = [] ) {
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

	public function output() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml">', "\n";

		$this->output_homepage();

		$query_args = self::get_query_args( [
			'post_type' => $this->post_type,
			'paged'     => $this->page,
		] );
		$query      = new \WP_Query( $query_args );

		foreach ( $query->posts as $post ) {
			if ( ! $this->is_indexed( $post ) ) {
				continue;
			}

			$this->current_post = $post;

			echo "\t<url>\n";
			echo "\t\t<loc>", esc_url( get_permalink( $post ) ), "</loc>\n";
			echo "\t\t<lastmod>", esc_html( gmdate( 'c', strtotime( $post->post_modified_gmt ) ) ), "</lastmod>\n";

			$images = $this->get_post_images( $post );
			array_walk( $images, [ $this, 'normalize_image' ] );
			$images = array_filter( $images );
			array_walk( $images, [ $this, 'output_image' ] );

			do_action( 'slim_seo_sitemap_post', $post );
			echo "\t</url>\n";
		}

		echo '</urlset>';
	}

	private function output_homepage() {
		if ( 'page' !== $this->post_type || 'posts' !== get_option( 'show_on_front' ) ) {
			return;
		}
		echo "\t<url>\n";
		echo "\t\t<loc>", esc_url( home_url( '/' ) ), "</loc>\n";
		echo "\t</url>\n";
	}

	private function output_image( $image ) {
		if ( empty( $image['url'] ) ) {
			return;
		}
		echo "\t\t<image:image>\n";
		echo "\t\t\t<image:loc>", esc_url( $this->get_absolute_url( $image['url'] ) ), "</image:loc>\n";
		echo "\t\t</image:image>\n";
	}

	private function normalize_image( &$image ) {
		if ( is_array( $image ) ) {
			return;
		}

		// If we get image URL only.
		if ( ! is_numeric( $image ) ) {
			$image = [ 'url' => $image ];
			return;
		}

		// Ignore if image is deleted.
		if ( ! get_attached_file( $image ) ) {
			$image = null;
			return;
		}

		$info       = wp_get_attachment_image_src( $image, 'full' );
		$attachment = get_post( $image );

		$caption = $attachment->post_excerpt;
		if ( empty( $caption ) ) {
			$caption = get_post_meta( $image, '_wp_attachment_image_alt', true );
		}

		$image = array_filter( [
			'url'     => $info[0],
			'title'   => $attachment->post_title,
			'caption' => $caption,
		] );
	}

	private function get_post_images( $post ) {
		$images = [];

		// Post thumbnail.
		$images[] = get_post_thumbnail_id( $post );

		// Get images from post content.
		$images = array_merge( $images, $this->get_images_from_html( $post->post_content ) );

		return array_filter( $images );
	}

	private function get_images_from_html( $html ) {
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
			if ( preg_match( '/wp-image-(\d+)/', $class, $matches ) && get_attached_file( $matches[1] ) ) {
				$values[] = (int) $matches[1];
				continue;
			}

			if ( $this->is_external( $src ) ) {
				continue;
			}

			$values[] = [
				'url'     => $src,
				'title'   => $image->getAttribute( 'title' ),
				'caption' => $image->getAttribute( 'alt' ),
			];
		}

		return $values;
	}

	private function is_external( string $url ): bool {
		$home_url = untrailingslashit( home_url() );
		return strpos( $url, $home_url ) === false;
	}

	private function get_absolute_url( $url ) {
		if ( wp_parse_url( $url, PHP_URL_SCHEME ) ) {
			return $url;
		}

		$url_parts = wp_parse_url( home_url() );

		// Non-protocol URL.
		if ( 0 === strpos( $url, '//' ) ) {
			return "{$url_parts['scheme']}:{$url}";
		}

		// Relative URL.
		return $url_parts['scheme'] . '://' . trailingslashit( $url_parts['host'] ) . ltrim( $url, '/' );
	}

	private function is_indexed( $post ) {
		$data = get_post_meta( $post->ID, 'slim_seo', true );
		return empty( $data['noindex'] );
	}
}
