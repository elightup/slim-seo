<?php
namespace SlimSEO;

use WP_Term;

class Breadcrumbs {
	private $args;
	private $links     = [];
	private $current   = '';
	private $is_parsed = false;

	public function __construct() {
		$this->args = [
			'separator'       => '&raquo;',
			'taxonomy'        => 'category',
			'display_current' => 'true',
			'label_home'      => __( 'Home', 'slim-seo' ),
			// Translators: search query.
			'label_search'    => __( 'Search Results for &#8220;%s&#8221;', 'slim-seo' ),
			'label_404'       => __( 'Page not found', 'slim-seo' ),
		];
	}

	public function setup(): void {
		add_shortcode( 'slim_seo_breadcrumbs', [ $this, 'render_shortcode' ] );

		register_block_type( SLIM_SEO_DIR . 'js/breadcrumbs/dist/', [
			'render_callback' => [ $this, 'render_block' ],
		] );
	}

	public function render_block( $attributes ): string {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return '<small><i>' . __( 'Due to the limitation of conditional tags in the admin, the preview of the breadcrumbs block is not available.', 'slim-seo' ) . '</i></small>';
		}

		$attributes['display_current'] = $attributes['display_current'] ? 'true' : 'false';
		return $this->render_shortcode( $attributes );
	}

	public function render_shortcode( $atts ): string {
		$this->args = wp_parse_args( $atts, $this->args );
		$this->parse();

		$links = $this->get_links();
		if ( empty( $links ) ) {
			return '';
		}

		$output = sprintf( '<nav class="breadcrumbs" aria-label="%s">', esc_attr__( 'Breadcrumbs', 'slim-seo' ) );

		// Links.
		$items    = [];
		$template = '<a href="%s" class="breadcrumb%s">%s</a>';
		foreach ( $links as $i => $item ) {
			$class   = 0 === $i ? ' breadcrumb--first' : '';
			$items[] = sprintf( $template, esc_url( $item['url'] ), $class, esc_html( wp_strip_all_tags( $item['text'] ) ), $i + 1 );
		}

		// Current page.
		if ( 'true' === $this->args['display_current'] ) {
			$items[] = sprintf( '<span class="breadcrumb breadcrumb--last" aria-current="page">%s</span>', esc_html( wp_strip_all_tags( $this->current ) ) );
		}

		$output .= implode( " <span class='breadcrumbs__separator'>{$this->args['separator']}</span> ", $items );
		$output .= '</nav>';

		return $output;
	}

	public function get_links(): array {
		return apply_filters( 'slim_seo_breadcrumbs_links', $this->links );
	}

	public function parse(): void {
		if ( $this->is_parsed ) {
			return;
		}

		// Allow developers to change the breadcrumbs args before parsing in real-time.
		$this->args = apply_filters( 'slim_seo_breadcrumbs_args', $this->args );

		if ( is_front_page() ) {
			return;
		}

		// Home.
		$this->add_link( home_url( '/' ), $this->args['label_home'] );

		if ( is_home() ) { // Static blog page.
			$this->current = single_post_title( '', false );
		} elseif ( is_post_type_archive() ) {
			$this->current = post_type_archive_title( '', false );
		} elseif ( is_singular() ) {
			$this->add_singular();
		} elseif ( is_tax() || is_category() || is_tag() ) { // Taxonomy archive.
			$term     = get_queried_object();
			$taxonomy = get_taxonomy( $term->taxonomy );
			if ( ! empty( $taxonomy->object_type ) && 1 === count( $taxonomy->object_type ) ) {
				$this->add_post_type_archive_link( reset( $taxonomy->object_type ) );
			}

			$this->add_term_ancestors( $term );
			$this->current = single_term_title( '', false );
		} elseif ( is_search() ) {
			$this->current = sprintf( $this->args['label_search'], get_search_query() );
		} elseif ( is_404() ) {
			$this->current = $this->args['label_404'];
		} elseif ( is_author() ) {
			$this->current = get_queried_object()->display_name;
		} elseif ( is_date() ) {
			$this->add_date_links();
		}

		$this->is_parsed = true;
	}

	private function add_singular(): void {
		$post          = get_queried_object();
		$this->current = single_post_title( '', false );

		$this->add_post_type_archive_link( $post->post_type );

		// If post type is hierarchical (like page), add ancestors.
		if ( is_post_type_hierarchical( $post->post_type ) ) {
			$ancestors = get_post_ancestors( $post );
			$ancestors = array_reverse( $ancestors );
			foreach ( $ancestors as $ancestor ) {
				$this->add_link( get_permalink( $ancestor ), get_the_title( $ancestor ) );
			}
		}

		// Add terms.
		$terms = get_the_terms( $post, $this->args['taxonomy'] );
		if ( ! is_array( $terms ) ) {
			return;
		}

		// Parse only first term and add its ancestors.
		$term = reset( $terms );
		$this->add_term_ancestors( $term );
		$this->add_link( get_term_link( $term ), $term->name );
	}

	private function add_post_type_archive_link( string $post_type ): void {
		// For posts, check if there's a static page for Blog archive.
		if ( 'post' === $post_type ) {
			$blog_page = get_option( 'page_for_posts' );
			if ( 'page' === get_option( 'show_on_front' ) && $blog_page ) {
				$this->add_link( get_permalink( $blog_page ), get_the_title( $blog_page ) );
			}
			return;
		}

		$post_type_object = get_post_type_object( $post_type );
		$link             = get_post_type_archive_link( $post_type );
		$text             = $post_type_object->labels->name;

		// If a page is set as the post type archive (like WooCommerce shop), then get title from that page.
		// Otherwise get from the post type archive settings.
		if ( is_string( $post_type_object->has_archive ) ) {
			$page = get_page_by_path( $post_type_object->has_archive );
			if ( $page ) {
				$text = get_the_title( $page );
			}
		}

		if ( $link ) {
			$this->add_link( $link, $text );
		}
	}

	private function add_term_ancestors( WP_Term $term ): void {
		$ancestors = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
		$ancestors = array_reverse( $ancestors );
		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $term->taxonomy );
			if ( $ancestor instanceof WP_Term ) {
				$this->add_link( get_term_link( $ancestor ), $ancestor->name );
			}
		}
	}

	private function add_date_links(): void {
		$year = get_query_var( 'year' );
		if ( is_year() ) {
			$this->current = $year;
			return;
		}

		global $wp_locale;
		$month       = get_query_var( 'monthnum' );
		$month_label = $month ? $wp_locale->get_month( $month ) : '';
		if ( is_month() ) {
			$this->add_link( get_year_link( $year ), $year );
			$this->current = $month_label;
			return;
		}

		$day = get_query_var( 'day' );
		$this->add_link( get_year_link( $year ), $year );
		$this->add_link( get_month_link( $year, $month ), $month_label );
		$this->current = zeroise( $day, 2 );
	}

	private function add_link( string $url, string $text ): void {
		$this->links[] = [
			'url'  => $url,
			'text' => $text,
		];
	}
}
