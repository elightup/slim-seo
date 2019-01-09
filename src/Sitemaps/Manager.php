<?php
namespace SlimSEO\Sitemaps;

class Manager {
	public function __construct() {
		add_action( 'init', [ $this, 'add_rewrite_rule' ] );
		add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
		add_action( 'template_redirect', [ $this, 'output' ], 0 );
		add_action( 'do_robotstxt', [ $this, 'add_to_robots_txt' ] );
	}

	public function add_rewrite_rule() {
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
		echo '<?xml-stylesheet type="text/xsl" href="', SLIM_SEO_URL, 'src/Sitemaps/style.xsl"?>', "\n";

		if ( WP_DEBUG ) {
			$timer_start  = microtime( true );
			$memory_start = memory_get_peak_usage();
		}

		if ( 'index' === $type ) {
			$sitemap = new Index();
			$sitemap->output();
		}

		if ( 0 === strpos( $type, 'post-type-' ) ) {
			$post_type = substr( $type, 10 );
			$sitemap   = new PostType( $post_type );
			$sitemap->output();
		}

		if ( 0 === strpos( $type, 'taxonomy-' ) ) {
			$taxonomy = substr( $type, 9 );
			$sitemap  = new Taxonomy( $taxonomy );
			$sitemap->output();
		}

		if ( WP_DEBUG ) {
			echo "\n" . '<!-- Memory peak usage: ' . number_format( ( memory_get_peak_usage() - $memory_start ) / 1024 / 1024, 3 ) . ' MB -->';
			echo "\n" . '<!-- Sitemap generation time: ' . number_format( microtime( true ) - $timer_start, 6 ) . ' seconds -->';
		}

		die;
	}

	public function add_to_robots_txt() {
		echo 'Sitemap: ', home_url( '/sitemap.xml' ), "\n";
	}
}
