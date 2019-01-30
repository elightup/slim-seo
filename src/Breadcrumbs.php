<?php
namespace SlimSEO;

class Breadcrumbs {
	private $args;
	private $items    = [];
	private $current  = '';
	private $position = 1;
	private $tpl_link;
	private $tpl_text;

	public function __construct() {
		add_shortcode( 'slim_seo_breadcrumbs', [ $this, 'output' ] );

		$this->tpl_link = '<span class="breadcrumb%s" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<a href="%s" itemprop="item"><span itemprop="name">%s</span></a>
				<meta itemprop="position" content="%d">
			</span>';
		$this->tpl_text = '<span class="breadcrumb breadcrumb--last" aria-current="page">%s</span>';
	}

	public function output( $atts ) {
		$this->set_args( $atts );

		$this->get_data();
		if ( ! $this->items ) {
			return '';
		}
		$this->add_current();

		$output  = $this->args['before'];
		$output .= sprintf( '<nav class="breadcrumbs" aria-label="%s" itemscope itemtype="http://schema.org/BreadcrumbList">', esc_attr__( 'Breadcrumbs', 'slim-seo' ) );
		$output .= implode( " {$this->args['separator']} ", $this->items );
		$output .= '</nav>';
		$output .= $this->args['after'];

		return $output;
	}

	private function set_args( $args ) {
		$this->args              = wp_parse_args(
			$args,
			array(
				'separator'       => '&raquo;',
				'before'          => '',
				'after'           => '',
				'taxonomy'        => 'category',
				'display_current' => true,
				'label_home'      => __( 'Home', 'slim-seo' ),
				// translators: search query.
				'label_search'    => __( 'Search results for: %s', 'slim-seo' ),
				'label_404'       => __( 'Not found', 'slim-seo' ),
				'label_archive'   => __( 'Archive', 'slim-seo' ),
			)
		);
		$this->args['separator'] = "<span class='breadcrumbs__separator'>{$this->args['separator']}</span>";
	}

	private function get_data() {
		if ( is_front_page() ) {
			return;
		}

		// Home.
		$this->add_link( home_url( '/' ), $this->args['label_home'] );

		// Blog.
		if ( is_home() ) {
			$this->current = get_the_title( get_option( 'page_for_posts' ) );
		}
		// Post type archive.
		elseif ( is_post_type_archive() ) {
			$this->current = get_post_type_object( get_post_type() )->labels->name;
		}
		// Single.
		elseif ( is_single() ) {
			$this->add_singular_with_terms();
		}
		// Page.
		elseif ( is_page() ) {
			$this->add_hierarchical_singular();
		}
		// Taxonomy archive.
		elseif ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			$this->add_term_ancestors( $term );
			$this->current = $term->name;
		}
		// Search results.
		elseif ( is_search() ) {
			$this->current = sprintf( $this->args['label_search'], get_search_query() );
		}
		// 404.
		elseif ( is_404() ) {
			$this->current = $this->args['label_404'];
		}
		// Author.
		elseif ( is_author() ) {
			$this->current = get_queried_object()->display_name;
		}
		// Day archive.
		elseif ( is_day() ) {
			$this->current = get_the_date();
		}
		// Month archive.
		elseif ( is_month() ) {
			$this->current = get_the_date( 'F Y' );
		}
		// Year archive.
		elseif ( is_year() ) {
			$this->current = get_the_date( 'Y' );
		}
		// General archive.
		else {
			$this->current = $this->args['label_archive'];
		}
	}

	private function add_hierarchical_singular() {
		$this->current = get_the_title();

		$this->add_post_type_archive_link();

		if ( ! is_post_type_hierarchical( get_post_type() ) ) {
			return;
		}

		$ancestors = get_post_ancestors( null );
		$ancestors = array_reverse( $ancestors );
		foreach ( $ancestors as $ancestor ) {
			$this->add_link( get_permalink( $ancestor ), get_the_title( $ancestor ) );
		}
	}

	private function add_singular_with_terms() {
		$this->current = get_the_title();

		$this->add_post_type_archive_link();

		// Terms.
		$terms = get_the_terms( get_the_ID(), $this->args['taxonomy'] );
		if ( ! is_array( $terms ) ) {
			return;
		}

		// Parse only first term and add its ancestors.
		$term = current( $terms );
		$this->add_term_ancestors( $term );
	}

	private function add_post_type_archive_link() {
		$post_type = get_post_type();

		// For posts, check if there's a static page for Blog archive.
		if ( 'post' === $post_type ) {
			if ( 'page' === get_option( 'show_on_front' ) && $blog_page = get_option( 'page_for_posts' ) ) {
				$this->add_link( get_permalink( $blog_page ), get_the_title( $blog_page ) );
			}
			return;
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( $link = get_post_type_archive_link( $post_type ) ) {
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

	private function add_link( $link, $text ) {
		$class         = 1 === $this->position ? ' breadcrumb--first' : '';
		$this->items[] = sprintf( $this->tpl_link, $class, esc_url( $link ), esc_html( wp_strip_all_tags( $text ) ), $this->position++ );
	}

	private function add_current() {
		if ( $this->args['display_current'] ) {
			$this->items[] = sprintf( $this->tpl_text, esc_html( wp_strip_all_tags( $this->current ) ) );
		}
	}
}
