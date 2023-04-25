<?php
namespace SlimSEO\Sitemaps;

class Manager {
	public function setup() {
		$this->add_rewrite_rules();
		add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
		add_action( 'template_redirect', [ $this, 'output' ], 0 );
		add_action( 'do_robotstxt', [ $this, 'add_to_robots_txt' ] );

		// Disable core sitemaps. Use `init` instead of `wp_sitemaps_enabled` to "completely" remove core sitemaps functionality, such as registering rewrite rules.
		remove_action( 'init', 'wp_sitemaps_get_server' );
	}

	public function add_rewrite_rules() {
		add_rewrite_rule( 'sitemap\.xml$', 'index.php?ss_sitemap=index', 'top' );
		add_rewrite_rule( 'sitemap-(post-type-[^/]+?)\.xml$', 'index.php?ss_sitemap=$matches[1]', 'top' );
		add_rewrite_rule( 'sitemap-(taxonomy-[^/]+?)\.xml$', 'index.php?ss_sitemap=$matches[1]', 'top' );

		if ( User::is_active() ) {
			add_rewrite_rule( 'sitemap-(user[^/]*?)\.xml$', 'index.php?ss_sitemap=$matches[1]', 'top' );
		}
	}

	public function add_query_vars( $vars ) {
		$vars[] = 'ss_sitemap';
		return $vars;
	}

	public function output() {
		$type = get_query_var( 'ss_sitemap' );
		if ( ! $type ) {
			return;
		}

		status_header( 200 );
		header( 'Content-type: text/xml; charset=utf-8', true );
		header( 'X-Robots-Tag: noindex, follow', true );

		echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
		echo '<?xml-stylesheet type="text/xsl" href="', esc_url( SLIM_SEO_URL ), 'src/Sitemaps/style.xsl"?>', "\n";

		$post_types = $this->get_allowed_post_types();
		$taxonomies = $this->get_allowed_taxonomies();

		if ( 'index' === $type ) {
			$sitemap = new Index( $post_types, $taxonomies );
			$sitemap->output();
		}

		if ( 0 === strpos( $type, 'post-type-' ) ) {
			$post_type = substr( $type, 10 );
			$page      = 1;
			if ( preg_match( '/(.+)-(\d+)$/', $post_type, $matches ) && post_type_exists( $matches[1] ) ) {
				$post_type = $matches[1];
				$page      = $matches[2];
			}
			if ( ! in_array( $post_type, $post_types, true ) ) {
				wp_die( esc_html__( 'Invalid sitemap URL.', 'slim-seo' ) );
			}

			$sitemap = new PostType( $post_type, $page );
			$sitemap->output();
		}

		if ( 0 === strpos( $type, 'taxonomy-' ) ) {
			$taxonomy = substr( $type, 9 );
			$page     = 1;
			if ( preg_match( '/(.+)-(\d+)$/', $taxonomy, $matches ) && taxonomy_exists( $matches[1] ) ) {
				$taxonomy = $matches[1];
				$page     = $matches[2];
			}
			if ( ! in_array( $taxonomy, $taxonomies, true ) ) {
				wp_die( esc_html__( 'Invalid sitemap URL.', 'slim-seo' ) );
			}

			$sitemap = new Taxonomy( $taxonomy, $page );
			$sitemap->output();
		}

		if ( User::is_active() && 0 === strpos( $type, 'user' ) ) {
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

	public function add_to_robots_txt() {
		echo "\nSitemap: ", esc_url( home_url( 'sitemap.xml' ) ), "\n";
	}

	private function get_allowed_post_types(): array {
		$post_types = get_post_types( [ 'public' => true ] );
		return (array) apply_filters( 'slim_seo_sitemap_post_types', $post_types );
	}

	private function get_allowed_taxonomies(): array {
		$taxonomies = get_taxonomies( [
			'public'  => true,
			'show_ui' => true,
		] );
		return (array) apply_filters( 'slim_seo_sitemap_taxonomies', $taxonomies );
	}
}
