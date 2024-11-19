<?php
namespace SlimSEO;

class NoCategoryBase {
	protected $option_name = 'ss_no_category_base';

	public function setup(): void {
		add_action( 'created_category', [ $this, 'no_category_base_refresh_rules' ] );
		add_action( 'delete_category', [ $this, 'no_category_base_refresh_rules' ] );
		add_action( 'edited_category', [ $this, 'no_category_base_refresh_rules' ] );
		add_action( 'init', [ $this, 'no_category_base_permastruct' ] );
		add_filter( 'category_rewrite_rules', [ $this, 'no_category_base_rewrite_rules' ] );
		add_filter( 'query_vars', [ $this, 'no_category_base_query_vars' ] );
		add_filter( 'request', [ $this, 'no_category_base_request' ] );

		$this->activate();
	}

	public function activate(): void {
		if ( get_option( $this->option_name, false ) ) {
			return;
		}

		update_option( $this->option_name, 1, false );

		$this->no_category_base_refresh_rules();
	}

	public function deactivate(): void {
		if ( ! get_option( $this->option_name, false ) ) {
			return;
		}

		delete_option( $this->option_name );

		$this->no_category_base_refresh_rules();
	}

	public function no_category_base_refresh_rules() {
		global $wp_rewrite;

		$wp_rewrite->flush_rules();
	}

	public function no_category_base_permastruct() {
		global $wp_rewrite;

		$wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
	}

	public function no_category_base_rewrite_rules( $category_rewrite ) {
		global $wp_rewrite;

		$category_rewrite = [];

		if ( class_exists( 'Sitepress' ) ) {
			global $sitepress;

			remove_filter( 'terms_clauses', [ $sitepress, 'terms_clauses' ] );

			$categories = get_categories( [ 'hide_empty' => false ] );

			add_filter( 'terms_clauses', [ $sitepress, 'terms_clauses' ], 10, 4 );
		} else {
			$categories = get_categories( [ 'hide_empty' => false ] );
		}

		foreach ( $categories as $category ) {
			$category_nicename = $category->slug;

			if ( $category->parent === $category->cat_ID ) {
				$category->parent = 0;
			} elseif ( 0 !== $category->parent ) {
				$category_nicename = get_category_parents( $category->parent, false, '/', true ) . $category_nicename;
			}

			$category_rewrite[ "({$category_nicename})/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$" ] = 'index.php?category_name=$matches[1]&feed=$matches[2]';

			$category_rewrite[ "({$category_nicename})/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$" ] = 'index.php?category_name=$matches[1]&paged=$matches[2]';

			$category_rewrite[ "({$category_nicename})/?$" ] = 'index.php?category_name=$matches[1]';
		}

		$old_category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
		$old_category_base = trim( $old_category_base, '/' );

		$category_rewrite[ $old_category_base . '/(.*)$' ] = 'index.php?category_redirect=$matches[1]';

		return $category_rewrite;
	}

	public function no_category_base_query_vars( $public_query_vars ) {
		$public_query_vars[] = 'category_redirect';

		return $public_query_vars;
	}

	public function no_category_base_request( $query_vars ) {
		if ( isset( $query_vars['category_redirect'] ) ) {
			$cat_link = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['category_redirect'], 'category' );

			status_header( 301 );

			header( "Location: $cat_link" );

			exit();
		}

		return $query_vars;
	}
}
