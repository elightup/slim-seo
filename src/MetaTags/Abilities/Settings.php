<?php
namespace SlimSEO\MetaTags\Abilities;

use WP_Error;
use eLightUp\SlimSEO\Common\Helpers\Data as CommonHelpersData;
use SlimSEO\Helpers\Data;
use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Description;

class Settings extends Base {
	protected $object_type   = 'settings';
	protected $has_canonical = false;
	protected $context       = '';
	protected $context_type  = '';

	protected function ability_config(): array {
		return [
			'slug'               => 'default',
			'get_label'          => __( 'Get default meta tags settings', 'slim-seo' ),
			'get_description'    => __( 'Retrieve default meta tags settings (post types, taxonomies and author). Returns title, description, social images and noindex for each context.', 'slim-seo' ),
			'update_label'       => __( 'Update default meta tags settings', 'slim-seo' ),
			'update_description' => __( 'Update default meta tags settings. Only provided fields are updated.', 'slim-seo' ),
		];
	}

	protected function resolve_input( array $input ) {
		if ( $this->context_type ) {
			return null;
		}

		$context = sanitize_key( wp_unslash( $input['context'] ?? '' ) );
		$error   = new WP_Error( 'slim_seo_abilities_invalid_context', __( 'Invalid settings context.', 'slim-seo' ) );

		if ( empty( $context ) ) {
			return $error;
		}

		$this->context = $context;

		if ( 'author' === $context ) {
			$this->context_type = $context;

			return null;
		}

		$post_types = Data::get_meta_box_post_types();

		if ( in_array( $context, $post_types, true ) ) {
			$this->context_type = 'post';

			return null;
		}

		$taxonomies = array_keys( CommonHelpersData::get_taxonomies() );

		if ( in_array( $context, $taxonomies, true ) ) {
			$this->context_type = 'term';

			return null;
		}

		return $error;
	}

	protected function input_props(): array {
		return [
			'context' => [
				'type'        => 'string',
				'description' => __( 'The settings context to retrieve. Use a post type slug (e.g. "post", "page") for post types, a taxonomy slug (e.g. "category", "post_tag") for taxonomies, "author" for author.', 'slim-seo' ),
			],
		];
	}

	protected function input_props_required(): array {
		return [ 'context' ];
	}

	protected function get_default(): array {
		return [
			'title'       => Title::DEFAULTS[ $this->context_type ] ?? '',
			'description' => Description::DEFAULTS[ $this->context_type ] ?? '',
		];
	}

	public function get_data( array $input ) {
		$error = $this->resolve_input( $input );

		if ( $error ) {
			return $error;
		}

		$settings = $this->get_settings();

		return $this->normalize_data( $settings[ $this->context ] ?? [] );
	}

	public function update_data( array $input ) {
		$error = $this->resolve_input( $input );

		if ( $error ) {
			return $error;
		}

		$settings = $this->get_settings();
		$data     = $this->sanitize_data( $settings[ $this->context ] ?? [], $input );

		if ( empty( $data ) ) {
			unset( $settings[ $this->context ] );
		} else {
			$settings[ $this->context ] = $data;
		}

		update_option( 'slim_seo', $settings );

		return [ 'success' => true ];
	}
}
