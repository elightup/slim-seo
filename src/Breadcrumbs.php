<?php
namespace SlimSEO;

use SlimSEO\Helpers\Data;
use WP_Post;
use WP_Post_Type;
use WP_Term;

class Breadcrumbs {
	/**
	 * Breadcrumbs arguments, used for both displaying and for schemas.
	 * @var array<string>
	 */
	private $args = [];

	/**
	 * Breadcrumbs links.
	 * @var array<int, array{url: string, text: string}>
	 */
	private $links = [];

	/**
	 * Current page title, e.g. the last item in the breadcrumbs.
	 * @var string|null
	 */
	private $current = '';

	/**
	 * Whether the breadcrumbs are parsed.
	 * @var bool
	 */
	private $is_parsed = false;

	public function setup(): void {
		$this->setup_args();

		add_shortcode( 'slim_seo_breadcrumbs', [ $this, 'render_shortcode' ] );

		register_block_type( SLIM_SEO_DIR . 'js/breadcrumbs/dist/', [
			'render_callback' => [ $this, 'render_block' ],
		] );
	}

	/**
	 * Setup args for breadcrumbs.
	 * Make it a public method to be called in Slim SEO Schema.
	 * Separate it into a method to call it after "init" hook to avoid loading text domain too early in WordPress 6.7.
	 */
	public function setup_args(): void {
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

	public function render_block( $attributes ): string {
		$attributes['display_current'] = $attributes['display_current'] ? 'true' : 'false';

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$this->prepare_for_block_preview( $attributes );
		}

		return $this->render_shortcode( $attributes );
	}

	private function prepare_for_block_preview( array $args ): void {
		$this->args = wp_parse_args( $args, $this->args );

		// Add sample links: Home » Category » Post title.
		$this->add_link( home_url( '/' ), $this->args['label_home'] );
		$this->add_link( '#', __( 'Parent', 'slim-seo' ) );

		if ( 'true' === $this->args['display_current'] ) {
			$this->current = __( 'Page title', 'slim-seo' );
		}

		$this->is_parsed = true;
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
			$items[] = sprintf( $template, esc_url( $item['url'] ), esc_attr( $class ), esc_html( $item['text'] ), $i + 1 );
		}

		// Current page.
		if ( 'true' === $this->args['display_current'] ) {
			$items[] = sprintf( '<span class="breadcrumb breadcrumb--last" aria-current="page">%s</span>', esc_html( $this->current ) );
		}

		$sep     = esc_html( $this->args['separator'] );
		$output .= implode( " <span class='breadcrumbs__separator' aria-hidden='true'>{$sep}</span> ", $items );
		$output .= '</nav>';

		return $output;
	}

	public function get_links(): array {
		return apply_filters( 'slim_seo_breadcrumbs_links', $this->links );
	}

	public function get_current_page(): string {
		return $this->args['display_current'] === 'true' ? (string) $this->current : '';
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

			// Post type archive can be used as the search results page (like in WooCommerce).
			// In that case, we need to show both the post type archive title and the search results.
			if ( is_search() ) {
				$post_type_object = get_queried_object();
				if ( $post_type_object instanceof WP_Post_Type ) {
					$this->add_post_type_archive_link( $post_type_object->name );
				}
				$this->current = sprintf( $this->args['label_search'], get_search_query() );
			}
		} elseif ( is_singular() ) {
			$this->add_singular();
		} elseif ( is_tax() || is_category() || is_tag() ) { // Taxonomy archive.
			$term = get_queried_object();
			if ( $term instanceof WP_Term ) {
				$taxonomy = get_taxonomy( $term->taxonomy );
				if ( ! empty( $taxonomy->object_type ) && 1 === count( $taxonomy->object_type ) ) {
					$this->add_post_type_archive_link( reset( $taxonomy->object_type ) );
				}

				$this->add_term_ancestors( $term );
			}
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
		$post = get_queried_object();
		if ( ! ( $post instanceof WP_Post ) ) {
			return;
		}

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
		if ( ! ( $post_type_object instanceof WP_Post_Type ) ) {
			return;
		}
		$text = $post_type_object->labels->name;

		// If a page is set as the post type archive (like WooCommerce shop), then get title from that page.
		// Otherwise get from the post type archive settings.
		$archive_page = Data::get_post_type_archive_page( $post_type );
		if ( $archive_page ) {
			if ( $archive_page->post_status !== 'publish' ) {
				return;
			}
			$text = get_the_title( $archive_page );
		}

		$link = get_post_type_archive_link( $post_type );
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
