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

		if ( 'index' === $type ) {
			$sitemap = new Index;
			$sitemap->output();
		}

		if ( 0 === strpos( $type, 'post-type-' ) ) {
			$post_type = substr( $type, 10 );
			$page      = 1;
			if ( preg_match( '/(.+)-(\d+)$/', $post_type, $matches ) && post_type_exists( $matches[1] ) ) {
				$post_type = $matches[1];
				$page      = $matches[2];
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
			$sitemap  = new Taxonomy( $taxonomy, $page );
			$sitemap->output();
		}

		die;
	}

	public function add_to_robots_txt() {
		echo 'Sitemap: ', esc_url( home_url( 'sitemap.xml' ) ), "\n";
	}
}
