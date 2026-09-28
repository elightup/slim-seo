<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;
use eLightUp\SlimSEO\Common\Helpers\Data as CommonHelpersData;

class PostTypeArchive extends Settings {
	protected $has_noindex = false;

	protected function ability_config(): array {
		return [
			'slug'               => 'post-type-archive',
			'get_label'          => __( 'Get post type archive meta tags', 'slim-seo' ),
			'get_description'    => __( 'Retrieve meta tags for a post type archive (title, description and social images).', 'slim-seo' ),
			'update_label'       => __( 'Update post type archive meta tags', 'slim-seo' ),
			'update_description' => __( 'Update meta tags for a post type archive. Only provided fields are updated.', 'slim-seo' ),
		];
	}

	protected function resolve_input( array $input ) {
		$this->reset_state();

		$context = sanitize_key( wp_unslash( $input['context'] ?? '' ) );
		$error   = new WP_Error( 'slim_seo_abilities_invalid_context', __( 'Invalid archive context.', 'slim-seo' ) );

		if ( empty( $context ) ) {
			return $error;
		}

		$archives = [];
		foreach ( CommonHelpersData::get_post_types() as $name => $post_type ) {
			if ( $post_type->has_archive ) {
				$archives[] = "{$name}_archive";
			}
		}

		if ( ! in_array( $context, $archives, true ) ) {
			return $error;
		}

		$this->context      = $context;
		$this->context_type = 'post_archive';

		return null;
	}

	protected function input_props(): array {
		return [
			'context' => [
				'type'        => 'string',
				'description' => __( 'Use a post type slug suffixed with "_archive" (e.g. "movie_archive").', 'slim-seo' ),
			],
		];
	}
}
