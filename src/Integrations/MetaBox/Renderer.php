<?php
namespace SlimSEO\Integrations\MetaBox;

use SlimSEO\Helpers\Arr;

class Renderer {
	private $meta_box;
	private $post_id = 0;
	private $term_id = 0;
	private $not_supported = [ 'background', 'fieldset_text', 'text_list', 'sidebar' ];

	public function __construct( $meta_box, int $post_id, int $term_id ) {
		$this->meta_box = $meta_box;
		$this->post_id  = $post_id;
		$this->term_id  = $term_id;
	}

	/**
	 * Must return true to make __get works.
	 */
	public function __isset( $name ): bool {
		return true;
	}

	/**
	 * Called each time Twig render a Meta Box's variable.
	 * If a prop contains 2 variables, like `{{ mb.field_group1.g.f1 }} - {{ mb.field_group1.g.f2 }}`, this method is called twice.
	 * In all cases, only the most outer field ID is passed. In the case above, only `g` is passed, not `g.f1` or `g.f2`.
	 */
	public function __get( $name ) {
		$field = Arr::find( $this->meta_box->fields, 'id', $name, __NAMESPACE__ . '\Id::normalize' );
		if ( ! $field || in_array( $field['type'], $this->not_supported, true ) ) {
			return null;
		}

		return $this->get_data( $field );
	}

	private function get_data( array $field ) {
		$object_id = $this->get_object_id();
		if ( ! $object_id ) {
			return null;
		}

		$args  = [ 'object_type' => $this->meta_box->get_object_type() ];
		$value = rwmb_get_value( $field['id'], $args, $object_id );

		if ( $field['type'] !== 'group' ) {
			return $this->parse_field_value( $value, $field );
		}
		$value = $this->parse_group_value( $value, $field );

		return $value;
	}

	private function get_object_id() {
		$object_type = $this->meta_box->get_object_type();

		// Post.
		if ( $object_type === 'post' ) {
			return $this->post_id ?: get_queried_object_id();
		}

		// Term.
		if ( $object_type === 'term' ) {
			return $this->term_id ?: get_queried_object_id();
		}

		// User.
		if ( $object_type === 'user' ) {
			return is_author() ? get_queried_object_id() : ( get_queried_object()->post_author ?? 0 );
		}

		// Settings pages.
		if ( ! in_array( $object_type, [ 'setting', 'network_setting' ], true ) ) {
			return get_queried_object_id();
		}

		$settings_pages = (array) $this->meta_box->settings_pages;
		if ( empty( $settings_pages ) ) {
			return '';
		}

		$settings_page = reset( $settings_pages );
		if ( ! $settings_page ) {
			return '';
		}

		$mb_settings_pages = apply_filters( 'mb_settings_pages', [] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		if ( empty( $mb_settings_pages ) ) {
			return '';
		}

		foreach ( $mb_settings_pages as $mb_settings_page ) {
			if ( $mb_settings_page['id'] === $settings_page ) {
				return $mb_settings_page['option_name'] ?? $settings_page;
			}
		}

		return '';
	}

	private function parse_group_value( $value, $field ) {
		if ( $field['clone'] ) {
			$value = (array) $value;
			$value = reset( $value );
		}

		$value = $this->parse_group_clone_value( $value, $field );
		return $value;
	}

	private function parse_group_clone_value( $value, $field ) {
		foreach ( $field['fields'] as $child ) {
			if ( empty( $child['id'] ) || empty( $value[ $child['id'] ] ) ) {
				continue;
			}
			$child_value = $value[ $child['id'] ];
			if ( 'group' === $child['type'] ) {
				$child_value = $this->parse_group_value( $child_value, $child );
			} else {
				$child_value = $this->parse_field_value( $child_value, $child );
			}

			$key           = Id::normalize( $child['id'] );
			$value[ $key ] = $child_value;
		}
		return $value;
	}

	private function parse_field_value( $value, $field ) {
		if ( in_array( $field['type'], [ 'button_group', 'checkbox_list', 'radio', 'select', 'select_advanced' ], true ) ) {
			$value = Fields\Choice::parse( $value, $field );
		}
		if ( in_array( $field['type'], [
			'file',
			'file_advanced',
			'file_upload',
			'image',
			'image_advanced',
			'image_upload',
			'plupload_image',
			'single_image',
			'video',
		], true ) ) {
			$value = Fields\File::parse( $value, $field );
		}
		if ( in_array( $field['type'], [ 'post' ], true ) ) {
			$value = Fields\Post::parse( $value, $field );
		}
		if ( in_array( $field['type'], [ 'taxonomy' ], true ) ) {
			$value = Fields\Taxonomy::parse( $value, $field );
		}
		if ( in_array( $field['type'], [ 'taxonomy_advanced' ], true ) ) {
			$value = Fields\TaxonomyAdvanced::parse( $value, $field );
		}
		if ( in_array( $field['type'], [ 'user' ], true ) ) {
			$value = Fields\User::parse( $value, $field );
		}

		return $value;
	}
}
