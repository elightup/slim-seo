<?php
namespace SlimSEO\Schema\Types;

class Article extends Base {
	protected $post;

	public function generate_schema() {
		$schema = [
			'@type'         => 'Article',
			'@id'           => $this->id,
			'url'           => $this->url,
			'headline'      => $this->post->post_title,
			'datePublished' => date( 'c', strtotime( get_queried_object()->post_date_gmt ) ),
			'dateModified'  => date( 'c', strtotime( get_queried_object()->post_modified_gmt ) ),
			'commentCount'  => (int) $this->post->comment_count,
		];

		if ( $this->parent ) {
			$schema['mainEntityOfPage'] = $this->parent->id;
		}

		$content = do_shortcode( $this->post->post_content );
		$content = wp_strip_all_tags( $content );

		$schema['wordCount'] = str_word_count( $content );

		if ( 'post' === $this->post->post_type ) {
			$tags = get_the_tags();
			if ( is_array( $tags ) ) {
				$schema['keywords'] = wp_list_pluck( $tags, 'name' );
			}

			$categories = get_the_category();
			if ( is_array( $categories ) ) {
				$schema['articleSection'] = wp_list_pluck( $categories, 'name' );
			}
		}

		return $schema;
	}
}