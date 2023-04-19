<?php
namespace SlimSEO\Migration;

use AIOSEO\Plugin\Common;

class AIOSEO extends Replacer {
	private $post;
	private $image;

	public function before_replace_post( $post_id ) {
		$this->post  = get_post( $post_id );
		$this->image = new ExtImage;

		set_current_screen( 'settings_page_slim-seo' ); // Fix undefined get_current_screen from AIOSEO.
	}

	public function get_post_title( $post_id ) {
		$title     = new Common\Meta\Title;
		$meta_data = aioseo()->meta->metaData->getMetaData( $this->post );

		return empty( $meta_data->title ) ? null : $title->helpers->prepare( $meta_data->title, $post_id );
	}

	public function get_post_description( $post_id ) {
		$description = new Common\Meta\Description;
		$meta_data   = aioseo()->meta->metaData->getMetaData( $this->post );

		return empty( $meta_data->description ) ? null : $description->helpers->prepare( $meta_data->description, $post_id, false, false );
	}

	public function get_post_facebook_image( $post_id ) {
		$meta_data = aioseo()->meta->metaData->getMetaData( $this->post );
		$image     = '';
		if ( ! empty( $meta_data ) ) {
			$image_source = ! empty( $meta_data->og_image_type ) && 'default' !== $meta_data->og_image_type
				? $meta_data->og_image_type
				: aioseo()->options->social->facebook->general->defaultImageSourcePosts;
			$image        = $this->get_image( 'facebook', $image_source, $this->post );
		}

		if ( $image ) {
			return is_array( $image ) ? $image[0] : $image;
		}
		return '';
	}

	public function get_post_twitter_image( $post_id ) {
		$meta_data = aioseo()->meta->metaData->getMetaData( $this->post );

		if ( ! empty( $meta_data->twitter_use_og ) ) {
			return $this->get_post_facebook_image( $post_id );
		}

		$image = '';
		if ( ! empty( $meta_data ) ) {
			$image_source = ! empty( $meta_data->twitter_image_type ) && 'default' !== $meta_data->twitter_image_type
				? $meta_data->twitter_image_type
				: aioseo()->options->social->twitter->general->defaultImageSourcePosts;
			$image        = $this->get_image( 'twitter', $image_source, $this->post );
		}

		$image = $image ? $image : $this->get_post_facebook_image( $post_id );
		if ( $image ) {
			return is_array( $image ) ? $image[0] : $image;
		}
		return '';
	}

	public function get_post_noindex( $post_id ) {
		$meta_data = aioseo()->meta->metaData->getMetaData( $this->post );

		return intval( $meta_data->robots_noindex );
	}

	public function get_image( $type, $image_source, $post ) {
		switch ( $image_source ) {
			case 'custom_image':
				$meta_data = aioseo()->meta->metaData->getMetaData( $post );
				if ( empty( $meta_data ) ) {
					break;
				}
				$image = ( 'facebook' === lcfirst( $type ) ) ? $meta_data->og_image_custom_url : $meta_data->twitter_image_custom_url;
				break;
			case 'default':
				$image = aioseo()->options->social->$type->general->defaultImagePosts;
				break;
			default:
				$image = $this->getAioImage( $type, $post );
		}

		if ( empty( $image ) ) {
			$image = aioseo()->options->social->$type->general->defaultImagePosts;
		}

		if ( is_array( $image ) ) {
			$images[ $type ] = $image;
			return $images[ $type ];
		}

		$attachment_id   = aioseo()->helpers->attachmentUrlToPostId( aioseo()->helpers->removeImageDimensions( $image ) );
		$images[ $type ] = $attachment_id ? wp_get_attachment_image_src( $attachment_id, $this->image->thumbnailSize ) : $image;
		return $images[ $type ];
	}


	public function getAioImage( $type, $post ) {
		global $wpdb;

		$column = 'og_image_url';
		if ( 'twitter' === $type ) {
			$column = 'twitter_image_url';
		}

		$image = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $column
				FROM {$wpdb->prefix}aioseo_posts
				WHERE post_id = %d",
				$post->ID
			)
		);

		return $image;
	}

	public function is_activated() {
		return defined( 'AIOSEO_VERSION' );
	}
}

if ( class_exists( 'AIOSEO\Plugin\Common\Social\Image' ) ) {
	class ExtImage extends Common\Social\Image {
		public function __get( $name ) {
			$method = ( 'get' . ucfirst( $name ) );
			if ( method_exists( $this, $method ) ) {
				return $this->$method();
			}
		}
		public function getThumbnailSize() {
			return $this->thumbnailSize;
		}
	}
}
