<?php
namespace SlimSEO;

class Breadcrumbs {
	private $args;
	private $items    = [];
	private $current  = '';
	private $position = 1;

	public function __construct() {
		add_shortcode( 'slim_seo_breadcrumbs', [ $this, 'output' ] );
	}

	public function output( $atts ) {
		$this->set_args( $atts );

		$this->get_data();
		if ( ! $this->items ) {
			return '';
		}
		$this->add_current();

		$output  = sprintf( '<nav class="breadcrumbs" aria-label="%s" itemscope itemtype="http://schema.org/BreadcrumbList">', esc_attr__( 'Breadcrumbs', 'slim-seo' ) );
		$output .= implode( " {$this->args['separator']} ", $this->items );
		$output .= '</nav>';

		return $output;
	}

	private function set_args( $args ) {
		$this->args              = wp_parse_args(
			$args,
			array(
				'separator'       => '&raquo;',
				'taxonomy'        => 'category',
				'display_current' => 'true',
				'label_home'      => __( 'Home', 'slim-seo' ),
				// translators: search query.
				'label_search'    => __( 'Search Results for &#8220;%s&#8221;', 'slim-seo' ),
				'label_404'       => __( 'Page not found', 'slim-seo' ),
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

		// Static blog page.
		if ( is_home() ) {
			$this->current = single_post_title( '', false );
		}
		// Post type archive.
		elseif ( is_post_type_archive() ) {
			$this->current = post_type_archive_title( '', false );
		}
		// Singular.
		elseif ( is_singular() ) {
			$this->add_singular();
		}
		// Taxonomy archive.
		elseif ( is_tax() || is_category() || is_tag() ) {
			$this->add_term_ancestors( get_queried_object() );
			$this->current = single_term_title( '', false );
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
		// Date archive.
		elseif ( is_date() ) {
			$this->add_date_links();
		}
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

	private function add_link( $link, $text ) {
		$template      = '<span class="breadcrumb%s" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
			<a href="%s" itemprop="item"><span itemprop="name">%s</span></a>
			<meta itemprop="position" content="%d">
		</span>';
		$class         = 1 === $this->position ? ' breadcrumb--first' : '';
		$this->items[] = sprintf( $template, $class, esc_url( $link ), esc_html( wp_strip_all_tags( $text ) ), $this->position++ );
	}

	private function add_current() {
		if ( 'true' === $this->args['display_current'] ) {
			$this->items[] = sprintf( '<span class="breadcrumb breadcrumb--last" aria-current="page">%s</span>', esc_html( wp_strip_all_tags( $this->current ) ) );
		}
	}
}
