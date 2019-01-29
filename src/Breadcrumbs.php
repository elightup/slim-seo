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
		$this->add_link_item( home_url( '/' ), $this->args['label_home'] );

		// Blog.
		if ( is_home() && ! is_front_page() ) {
			$page          = get_option( 'page_for_posts' );
			$this->current = get_the_title( $page );
		}
		// Post type archive.
		elseif ( is_post_type_archive() ) {
			$post_type = get_post_type();
			if ( 'post' !== $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				$this->current    = $post_type_object->labels->name;
			}
		}
		// Single.
		elseif ( is_single() ) {
			// Add post type archive link.
			$post_type = get_post_type();
			if ( 'post' !== $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				$this->add_link_item( get_post_type_archive_link( $post_type ), $post_type_object->labels->name );
			}

			// Terms.
			$terms = get_the_terms( get_the_ID(), $this->args['taxonomy'] );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term    = current( $terms );
				$terms   = $this->get_term_parents( $term->term_id, $this->args['taxonomy'] );
				$terms[] = $term->term_id;
				foreach ( $terms as $term_id ) {
					$term = get_term( $term_id, $this->args['taxonomy'] );
					$this->add_link_item( get_term_link( $term, $this->args['taxonomy'] ), $term->name );
				}
			}

			$this->current = get_the_title();
		}
		// Page.
		elseif ( is_page() ) {
			$pages = $this->get_post_parents( get_queried_object_id() );
			foreach ( $pages as $page ) {
				$this->add_link_item( get_permalink( $page ), get_the_title( $page ) );
			}
			$this->current = get_the_title();
		}
		// Taxonomy archive.
		elseif ( is_tax() || is_category() || is_tag() ) {
			$current_term = get_queried_object();
			$terms        = $this->get_term_parents( get_queried_object_id(), $current_term->taxonomy );
			foreach ( $terms as $term_id ) {
				$term = get_term( $term_id, $current_term->taxonomy );
				$this->add_link_item( get_category_link( $term_id ), $term->name );
			}
			$this->current = $current_term->name;
		}
		// Search results.
		elseif ( is_search() ) {
			// translators: search query.
			$this->current = sprintf( $this->args['label_search'], get_search_query() );
		}
		// 404.
		elseif ( is_404() ) {
			$this->current = $this->args['label_404'];
		}
		// Author.
		elseif ( is_author() ) {
			// Queue the first post, that way we know what author we're dealing with (if that is the case).
			the_post();
			$this->current = get_the_author();
			rewind_posts();
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

	private function add_link_item( $link, $text ) {
		$class         = 1 === $this->position ? ' breadcrumb--first' : '';
		$this->items[] = sprintf( $this->tpl_link, $class, esc_url( $link ), esc_html( wp_strip_all_tags( $text ) ), $this->position++ );
	}

	private function add_current() {
		if ( $this->args['display_current'] ) {
			$this->items[] = sprintf( $this->tpl_text, esc_html( wp_strip_all_tags( $this->current ) ) );
		}
	}

	/**
	 * Searches for term parents' IDs of hierarchical taxonomies, including current term.
	 * This function is similar to the WordPress function get_category_parents() but handles any type of taxonomy.
	 *
	 * @param int|string    $term_id  The term ID.
	 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
	 *
	 * @return array Array of parent terms' IDs.
	 */
	private function get_term_parents( $term_id = '', $taxonomy = 'category' ) {
		$list = array();
		if ( empty( $term_id ) || empty( $taxonomy ) ) {
			return $list;
		}

		do {
			$list[] = $term_id;

			// Get next parent term.
			$term    = get_term( $term_id, $taxonomy );
			$term_id = $term->parent;
		} while ( $term_id );

		// Reverse the array to put them in the proper order for the trail.
		$list = array_reverse( $list );
		array_pop( $list );

		return $list;
	}

	/**
	 * Gets parent posts' IDs, include current post.
	 *
	 * @param int|string $post_id ID of the post whose parents we want.
	 *
	 * @return array Array of parent posts' IDs.
	 */
	private function get_post_parents( $post_id = '' ) {
		$list = array();
		if ( empty( $post_id ) ) {
			return $list;
		}

		do {
			$list[] = $post_id;

			// Get next parent post.
			$post    = get_post( $post_id );
			$post_id = $post->post_parent;
		} while ( $post_id );

		// Reverse the array to put them in the proper order for the trail.
		$list = array_reverse( $list );
		array_pop( $list );

		return $list;
	}
}
