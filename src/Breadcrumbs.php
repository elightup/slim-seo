<?php
namespace SlimSEO;

class Breadcrumbs {
	private $args;
	private $links     = [];
	private $current   = '';
	private $is_parsed = false;

	public function setup() {
		$this->args = array(
			'separator'       => '&raquo;',
			'taxonomy'        => 'category',
			'display_current' => 'true',
			'label_home'      => __( 'Home', 'slim-seo' ),
			// translators: search query.
			'label_search'    => __( 'Search Results for &#8220;%s&#8221;', 'slim-seo' ),
			'label_404'       => __( 'Page not found', 'slim-seo' ),
		);

		add_shortcode( 'slim_seo_breadcrumbs', [ $this, 'render_shortcode' ] );
	}

	public function render_shortcode( $atts ) {
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

	public function get_links() {
		return apply_filters( 'slim_seo_breadcrumbs_links', $this->links );
	}

	public function parse() {
		if ( $this->is_parsed ) {
			return;
		}

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
			$this->add_term_ancestors( get_queried_object() );
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

	private function add_singular() {
		$this->current = single_post_title( '', false );

		$this->add_post_type_archive_link();

		// If post type is hierarchical (like page), then output its ancestors.
		if ( is_post_type_hierarchical( get_post_type() ) ) {
			$ancestors = get_post_ancestors( null );
			$ancestors = array_reverse( $ancestors );
			foreach ( $ancestors as $ancestor ) {
				$this->add_link( get_permalink( $ancestor ), get_the_title( $ancestor ) );
			}
			return;
		}

		// For non-hierarchical post type (like post), output its terms.
		$terms = get_the_terms( get_the_ID(), $this->args['taxonomy'] );
		if ( ! is_array( $terms ) ) {
			return;
		}

		// Parse only first term and add its ancestors.
		$term = reset( $terms );
		$this->add_term_ancestors( $term );
		$this->add_link( get_term_link( $term ), $term->name );
	}

	private function add_post_type_archive_link() {
		$post_type = get_post_type();

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
		if ( $link ) {
			$this->add_link( $link, $post_type_object->labels->name );
		}
	}

	private function add_term_ancestors( $term ) {
		$ancestors = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
		$ancestors = array_reverse( $ancestors );
		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $term->taxonomy );
			$this->add_link( get_term_link( $ancestor ), $ancestor->name );
		}
	}

	private function add_date_links() {
		$time        = strtotime( get_the_date( 'c' ) );
		$year        = get_the_date( 'Y' );
		$year_label  = date_i18n( 'Y', $time ); // Use date_i18n to show date in localized format.
		$month       = get_the_date( 'm' );
		$month_label = date_i18n( 'F', $time );

		if ( is_year() ) {
			$this->current = $year_label;
			return;
		}

		if ( is_month() ) {
			$this->add_link( get_year_link( $year ), $year_label );
			$this->current = $month_label;
			return;
		}

		$this->add_link( get_year_link( $year ), $year_label );
		$this->add_link( get_month_link( $year, $month ), $month_label );
		$this->current = date_i18n( 'd', $time );
	}

	private function add_link( $url, $text ) {
		$this->links[] = [
			'url'  => $url,
			'text' => $text,
		];
	}
}
