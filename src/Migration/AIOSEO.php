<?php
namespace SlimSEO\Migration;

class AIOSEO extends Replacer {

	public function before_replace_post( $post_id ) {
		$this->post     = get_post( $post_id );
	}

	public function get_post_title( $post_id ) {
		$meta = get_post_meta( $post_id, '_aioseo_title', true );
		$title = new \AIOSEO\Plugin\Common\Meta\Title();
		return $title->prepareTitle( $meta, $post_id );
	}

	public function get_post_description( $post_id ) {
		$meta        = get_post_meta( $post_id, '_aioseo_description', true );
		$description = new \AIOSEO\Plugin\Common\Meta\Description();
		return $description->prepareDescription( $meta, $post_id );
	}

	public function get_post_facebook_image( $post_id ) {
		$metaData = aioseo()->meta->metaData->getMetaData( $this->post );
		$image = '';
		if ( ! empty( $metaData ) ) {
			$imageSource = ! empty( $metaData->og_image_type ) && 'default' !== $metaData->og_image_type
				? $metaData->og_image_type
				: aioseo()->options->social->facebook->general->defaultImageSourcePosts;

			$image = aioseo()->social->image->getImage( 'facebook', $imageSource, $this->post );
		}
		if ( $image ) {
			return is_array( $image ) ? $image[0] : $image;
		}
		return '';
	}

	public function get_post_twitter_image( $post_id ) {
		$metaData = aioseo()->meta->metaData->getMetaData( $this->post );

		if ( ! empty( $metaData->twitter_use_og ) ) {
			return $this->get_post_facebook_image( $post_id );
		}

		$image = '';
		if ( ! empty( $metaData ) ) {
			$imageSource = ! empty( $metaData->twitter_image_type ) && 'default' !== $metaData->twitter_image_type
				? $metaData->twitter_image_type
				: aioseo()->options->social->twitter->general->defaultImageSourcePosts;

			$image = aioseo()->social->image->getImage( 'twitter', $imageSource, $this->post );
		}

		$image = $image ? $image : $this->get_post_facebook_image( $post_id );
		if ( $image ) {
			return is_array( $image ) ? $image[0] : $image;
		}
		return '';
	}

	public function cleanup_posts() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ('_aioseo_title', '_aioseo_description')" );
	}
}
