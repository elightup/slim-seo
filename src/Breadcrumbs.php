<?php
namespace SlimSEO;

class Breadcrumbs {
	public function __construct() {
		add_shortcode( 'slim_seo_breadcrumbs', [$this, 'output']);
	}

	public function output( $atts = '' ) {
		if ( is_front_page() ) {
			return;
		}

		$atts = wp_parse_args(
			$atts,
			array(
				'separator'         => '&raquo;',
				'home_label'        => esc_html__( 'Home', 'slim-seo' ),
				'home_class'        => 'home',
				'before'            => '<div class="breadcrumbs">',
				'after'             => '</div>',
				'before_item'       => '<span class="breadcrumbs-item">',
				'after_item'        => '</span>',
				'taxonomy'          => 'category',
				'display_last_item' => true,
			)
		);
		$atts = apply_filters( 'slim_seo_breadcrumbs_args', $atts );

		$items = array();

		$title = '';

		$description = '';

		// HTML template for each item.
		$item_tpl_link = $atts['before_item'] . '
			<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
			<a href="%s" itemprop="url"><span itemprop="title">%s</span></a>
			</span>
			' . $atts['after_item'];
		$item_text_tpl = $atts['before_item'] . '
			<span class="last-item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
			<span itemprop="title">%s</span>
			</span>
			' . $atts['after_item'];

		// Home.
		if ( ! $atts['home_class'] ) {
			$items[] = sprintf( $item_tpl_link, esc_url( home_url( '/' ) ), $atts['home_label'] );
		} else {
			$items[] = $atts['before_item'] . sprintf(
				'<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
				<a class="%s" href="%s" itemprop="url"><span itemprop="title">%s</span></a>
				</span>' . $atts['after_item'],
				esc_attr( $atts['home_class'] ),
				esc_url( home_url() ),
				$atts['home_label']
			);
		}

		if ( is_home() && ! is_front_page() ) {
			$page = get_option( 'page_for_posts' );
			if ( $atts['display_last_item'] ) {
				$title       = get_the_title( $page );
				$description = get_the_excerpt( $page );
			}
		} elseif ( is_post_type_archive() ) {

			// If post is a custom post type.
			$post_type = get_post_type();
			if ( 'post' !== $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				$title            = $post_type_object->labels->name;
			}
		} elseif ( is_single() ) {

			// If post is a custom post type.
			$post_type = get_post_type();
			if ( 'post' !== $post_type ) {
				$post_type_object       = get_post_type_object( $post_type );
				$post_type_archive_link = get_post_type_archive_link( $post_type );
				$items[]                = sprintf( $item_tpl_link, $post_type_archive_link, $post_type_object->labels->u_name );
			}
			// Terms.
			$terms = get_the_terms( get_the_ID(), $atts['taxonomy'] );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term    = current( $terms );
				$terms   = $this->get_term_parents( $term->term_id, $atts['taxonomy'] );
				$terms[] = $term->term_id;
				foreach ( $terms as $term_id ) {
					$term    = get_term( $term_id, $atts['taxonomy'] );
					$items[] = sprintf( $item_tpl_link, get_term_link( $term, $atts['taxonomy'] ), $term->name );
				}
			}

			if ( $atts['display_last_item'] ) {
				$title = get_the_title();
				if ( has_excerpt() ) {
					$description = get_the_excerpt();
				}
			}
		} elseif ( is_page() ) {
			$pages = $this->get_post_parents( get_queried_object_id() );
			foreach ( $pages as $page ) {
				$items[] = sprintf( $item_tpl_link, get_permalink( $page ), get_the_title( $page ) );
			}
			if ( $atts['display_last_item'] ) {
				$title = get_the_title();
				if ( has_excerpt() ) {
					$description = get_the_excerpt();
				}
			}
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$current_term = get_queried_object();
			$terms        = $this->get_term_parents( get_queried_object_id(), $current_term->taxonomy );
			foreach ( $terms as $term_id ) {
				$term    = get_term( $term_id, $current_term->taxonomy );
				$items[] = sprintf( $item_tpl_link, get_category_link( $term_id ), $term->name );
			}
			if ( $atts['display_last_item'] ) {
				$title       = $current_term->name;
				$description = $current_term->description;
			}
		} elseif ( is_search() ) {
			/* translators: search query */
			$title = sprintf( esc_html__( 'Search results for &quot;%s&quot;', 'slim-seo' ), get_search_query() );
		} elseif ( is_404() ) {
			$title = esc_html__( 'Not Found', 'slim-seo' );
		} elseif ( is_author() ) {
			// Queue the first post, that way we know what author we're dealing with (if that is the case).
			the_post();
			$title = sprintf(
				'%s',
				'<span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>'
			);
			rewind_posts();
		} elseif ( is_day() ) {
			$title = sprintf( esc_html( '%s', 'slim-seo' ), get_the_date() );
		} elseif ( is_month() ) {
			$title = sprintf( esc_html( '%s', 'slim-seo' ), get_the_date( 'F Y' ) );
		} elseif ( is_year() ) {
			$title = sprintf( esc_html( '%s', 'slim-seo' ), get_the_date( 'Y' ) );
		} else {
			$title = esc_html__( 'Archives', 'slim-seo' );
		} // End if().
		$items[] = sprintf( $item_text_tpl, $title );
		if ( is_single() ) {
			$title = '<div class="page-header-title"><h1 class="entry-title">' . esc_html( $title ) . '<h2 class="entry-description">' . esc_html( $description ) . '</h2></h1></div>';
		} else {
			$title = '<div class="page-header-title"><h1>' . wp_kses_post( $title ) . '<h2 class="entry-description">' . wp_kses_post( $description ) . '</h2></h1></div>';
		}
		return $atts['before'] . implode( $atts['separator'], $items ) . $atts['after'] . $title; // WPCS: XSS OK.
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
