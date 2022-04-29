<?php
namespace SlimSEO\Schema\Types;

class Article extends Base {
	public function generate() {
		$post   = get_queried_object();
		$schema = [
			'@type'         => 'Article',
			'@id'           => $this->id,
			'url'           => $this->url,
			'headline'      => $post->post_title,
			'datePublished' => gmdate( 'c', strtotime( $post->post_date_gmt ) ),
			'dateModified'  => gmdate( 'c', strtotime( $post->post_modified_gmt ) ),
			'commentCount'  => (int) $post->comment_count,
		];

		$content = do_shortcode( $post->post_content );
		$content = wp_strip_all_tags( $content );

		$schema['wordCount'] = str_word_count( $content );

		if ( 'post' === $post->post_type ) {
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
