<?php
namespace SlimSEO\Sitemaps;

use SlimSEO\Helpers\Data;

class Manager {
	private $post_types = [];
	private $taxonomies = [];

	public function setup(): void {
		add_action( 'init', [ $this, 'add_rewrite_rules' ], 20 ); // Priority 20 to make sure all post types & taxonomies are registered.
		add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
		add_action( 'template_redirect', [ $this, 'output' ], 0 );
		add_filter( 'robots_txt', [ $this, 'add_to_robots_txt' ], 20 ); // Priority 20 to output it below user-agent rules.

		// Disable core sitemaps. Use `init` instead of `wp_sitemaps_enabled` to "completely" remove core sitemaps functionality, such as registering rewrite rules.
		remove_action( 'init', 'wp_sitemaps_get_server' );
	}

	public function add_rewrite_rules(): void {
		$this->get_post_types();
		$this->get_taxonomies();

		$has_sitemap = false;

		if ( ! empty( $this->post_types ) ) {
			add_rewrite_rule( 'sitemap-(post-type-[^/]+?)\.xml$', 'index.php?ss_sitemap=$matches[1]', 'top' );
			$has_sitemap = true;
		}
		if ( ! empty( $this->taxonomies ) ) {
			add_rewrite_rule( 'sitemap-(taxonomy-[^/]+?)\.xml$', 'index.php?ss_sitemap=$matches[1]', 'top' );
			$has_sitemap = true;
		}
		if ( User::is_active() ) {
			add_rewrite_rule( 'sitemap-(user[^/]*?)\.xml$', 'index.php?ss_sitemap=$matches[1]', 'top' );
			$has_sitemap = true;
		}

		if ( $has_sitemap ) {
			add_rewrite_rule( 'sitemap\.xml$', 'index.php?ss_sitemap=index', 'top' );
		}
	}

	public function add_query_vars( array $vars ): array {
		$vars[] = 'ss_sitemap';
		return $vars;
	}

	public function output(): void {
		$type = get_query_var( 'ss_sitemap' );
		if ( ! $type ) {
			return;
		}

		do_action( 'slim_seo_sitemap_before_output' );

		status_header( 200 );
		header( 'Content-type: text/xml; charset=utf-8', true );
		header( 'X-Robots-Tag: noindex, follow', true );

		echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";

		if ( apply_filters( 'slim_seo_sitemap_style', true ) ) {
			echo '<?xml-stylesheet type="text/xsl" href="', esc_url( SLIM_SEO_URL ), 'src/Sitemaps/style.xsl"?>', "\n";
		}

		if ( 'index' === $type ) {
			$sitemap = new Index( $this->post_types, $this->taxonomies );
			$sitemap->output();
		}

		if ( str_starts_with( $type, 'post-type-' ) ) {
			$post_type = substr( $type, 10 );
			$page      = 1;
			if ( preg_match( '/(.+)-(\d+)$/', $post_type, $matches ) && post_type_exists( $matches[1] ) ) {
				$post_type = $matches[1];
				$page      = (int) $matches[2];
			}
			if ( ! in_array( $post_type, $this->post_types, true ) ) {
				wp_die( esc_html__( 'Invalid sitemap URL.', 'slim-seo' ) );
			}

			$sitemap = new PostType( $post_type, $page );
			$sitemap->output();
		}

		if ( str_starts_with( $type, 'taxonomy-' ) ) {
			$taxonomy = substr( $type, 9 );
			$page     = 1;
			if ( preg_match( '/(.+)-(\d+)$/', $taxonomy, $matches ) && taxonomy_exists( $matches[1] ) ) {
				$taxonomy = $matches[1];
				$page     = (int) $matches[2];
			}
			if ( ! in_array( $taxonomy, $this->taxonomies, true ) ) {
				wp_die( esc_html__( 'Invalid sitemap URL.', 'slim-seo' ) );
			}

			$sitemap = new Taxonomy( $taxonomy, $page );
			$sitemap->output();
		}

		if ( User::is_active() && str_starts_with( $type, 'user' ) ) {
			$user = substr( $type, 4 );
			$page = 1;
			if ( preg_match( '/-(\d+)$/', $user, $matches ) ) {
				$page = (int) $matches[1];
			}

			$sitemap = new User( $page );
			$sitemap->output();
		}

		die;
	}

	public function add_to_robots_txt( string $output ): string {
		return $output . "\nSitemap: " . home_url( 'sitemap.xml' ) . "\n";
	}

	private function get_post_types(): void {
		$option     = get_option( 'slim_seo' );
		$post_types = array_keys( Data::get_post_types() );
		$post_types = array_filter( $post_types, function ( $post_type ) use ( $option ) {
			return empty( $option[ $post_type ]['noindex'] );
		} );

		$this->post_types = (array) apply_filters( 'slim_seo_sitemap_post_types', array_values( $post_types ) );
	}

	private function get_taxonomies(): void {
		$taxonomies = array_keys( Data::get_taxonomies() );

		$this->taxonomies = (array) apply_filters( 'slim_seo_sitemap_taxonomies', $taxonomies );
	}
}
