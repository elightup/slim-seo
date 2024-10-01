<?php
namespace SlimSEO\MetaTags;

defined( 'ABSPATH' ) || die;

use WP_Term;
use SlimSEO\Helpers\Option;

class Robots {
	use Context;

	private $url;

	public function __construct( CanonicalUrl $url ) {
		$this->url = $url;
	}

	public function setup() {
		if ( '0' === get_option( 'blog_public' ) ) {
			return;
		}

		add_filter( 'wp_robots', [ $this, 'modify_robots' ] );

		add_action( 'template_redirect', [ $this, 'set_header_noindex' ] );
		add_filter( 'loginout', [ $this, 'set_link_nofollow' ] );
		add_filter( 'register', [ $this, 'set_link_nofollow' ] );

		add_filter( 'robots_txt', [ $this, 'add_to_robots_txt' ] );
	}

	public function modify_robots( $robots ) {
		if ( $this->indexed() ) {
			$robots['max-snippet']       = '-1';
			$robots['max-video-preview'] = '-1';
			return $robots;
		}

		// No index.
		$this->remove_canonical_link();
		return [
			'noindex' => true,
			'follow'  => true,
		];
	}

	private function remove_canonical_link() {
		remove_action( 'wp_head', [ $this->url, 'output' ] );
		remove_action( 'wp_head', 'rel_canonical' );
	}

	private function indexed() {
		$value = $this->get_indexed();
		return apply_filters( 'slim_seo_robots_index', $value, $this->get_queried_object_id() );
	}

	private function get_indexed() {
		// Do not index search or 404 page.
		if ( is_search() || is_404() ) {
			return false;
		}

		// Do not index private posts.
		if ( is_singular() && 'private' === get_queried_object()->post_status ) {
			return false;
		}

		// Do not index pages with no content.
		global $wp_query;
		if ( ! is_front_page() && ! $wp_query->post_count ) {
			return false;
		}

		$noindex = $this->get_value();
		return ! $noindex;
	}

	private function get_post_type_archive_value(): bool {
		$post_type_object = get_queried_object();
		return (bool) Option::get( "{$post_type_object->name}.noindex", false );
	}

	/**
	 * Make public to allow access from other class.
	 * @see AdminColumns/Post.php.
	 */
	public function get_singular_value( $post_id = 0 ): bool {
		$post_id   = $post_id ?: $this->get_queried_object_id();
		$post_type = get_post_type( $post_id );

		$post_type_noindex = (bool) Option::get( "{$post_type}.noindex", false );

		$data         = get_post_meta( $post_id, 'slim_seo', true );
		$post_noindex = (bool) ( $data['noindex'] ?? false );

		return $post_noindex || $post_type_noindex;
	}

	/**
	 * Make public to allow access from other class.
	 * @see AdminColumns/Term.php.
	 */
	public function get_term_value( $term_id = 0 ): bool {
		$term_id = $term_id ?: get_queried_object_id();
		$term    = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return false;
		}

		$taxonomy_noindex = (bool) Option::get( "{$term->taxonomy}.noindex", false );

		$data         = get_term_meta( $term_id, 'slim_seo', true );
		$term_noindex = (bool) ( $data['noindex'] ?? false );

		return $term_noindex || $taxonomy_noindex;
	}

	/**
	 * Set noindex for X-Robots-Tag header to make pages noindexed.
	 */
	public function set_header_noindex() {
		if ( ( is_feed() || is_robots() ) && headers_sent() === false ) {
			header( 'X-Robots-Tag: noindex, follow', true );
		}
	}

	public function set_link_nofollow( $link ) {
		return str_replace( '<a ', '<a rel="nofollow" ', $link );
	}

	public function add_to_robots_txt( string $output ): string {
		$content  = "Disallow: /?s=\n";
		$content .= "Disallow: /page/*/?s=\n";
		$content .= "Disallow: /search/\n";

		$content = apply_filters( 'slim_seo_robots_txt', $content );
		$output  = str_replace( 'Allow:', "{$content}Allow:", $output );

		return $output;
	}

	private function get_author_value(): bool {
		return (bool) Option::get( 'author.noindex', false );
	}
}
