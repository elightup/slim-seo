<?php
namespace SlimSEO\Schema\Types;

use SlimSEO\MetaTags\Data;
use SlimSEO\MetaTags\Helper;

class Article extends Base {
	public function generate() {
		$post   = get_queried_object();
		$schema = [
			'@type'         => 'Article',
			'@id'           => $this->id,
			'url'           => $this->url,
			'headline'      => Helper::sanitize_post_shortcodes( (string) $post->post_title, $post ),
			'datePublished' => wp_date( 'c', strtotime( $post->post_date_gmt ) ),
			'dateModified'  => wp_date( 'c', strtotime( $post->post_modified_gmt ) ),
			'commentCount'  => (int) $post->comment_count,
		];

		$content = Helper::normalize( Data::get_post_content( $post->ID ) );

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
