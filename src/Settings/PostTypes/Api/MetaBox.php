<?php
namespace SlimSEO\Settings\PostTypes\Api;

class MetaBox {
	private $variables;

	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init() {
		if ( ! defined( 'RWMB_VER' ) ) {
			return;
		}

		add_filter( 'slim_seo_variables', [ $this, 'add_variables' ] );
	}

	public function add_variables( $variables ) {
		$this->variables = $variables;

		$meta_boxes = $this->get_meta_boxes();
		array_walk( $meta_boxes, [ $this, 'add_group' ] );

		return $this->variables;
	}

	private function get_meta_boxes() {
		$meta_boxes = rwmb_get_registry( 'meta_box' )->all();
		$meta_boxes = array_filter( $meta_boxes, [ $this, 'remove_built_in' ] );

		return $meta_boxes;
	}

	private function remove_built_in( $meta_box ) {
		$built_in = [
			// MB Favorite Posts.
			'mbfp-preview-section',
			'mbfp_posts',

			// MB Views.
			'mbv-template-editor',
			'mbv-settings',
			'mbv-shortcode',

			// MB User Profile.
			'rwmb-user-register',
			'rwmb-user-login',
			'rwmb-user-lost-password',
			'rwmb-user-reset-password',
			'rwmb-user-info',
		];

		$is_relationship = preg_match( '/(_relationships_from|_relationships_to)$/', $meta_box->id );
		return ! in_array( $meta_box->id, $built_in, true ) && ! $is_relationship;
	}

	private function add_group( $meta_box ) {
		$key               = $this::normalize( $meta_box->id );
		$this->variables[] = [
			'label'   => "[Meta Box] {$meta_box->title}",
			'options' => $this->add_fields( $meta_box->fields, "mb.$key" ),
		];
	}

	private function add_fields( $fields, $base_id = '', $indent = '' ) {
		$options    = [];
		$fields     = array_filter( $fields, [ $this, 'has_value' ] );
		$sub_indent = $indent . str_repeat( '&nbsp;', 5 );

		foreach ( $fields as $field ) {
			$key   = $this::normalize( $field['id'] );
			$id    = "$base_id.$key";
			$label = "{$indent}{$field['name']}";

			if ( in_array( $field['type'], [ 'map', 'osm' ], true ) ) {
				$options[ $id . '.latitude' ]  = sprintf( __( '%s (latitude)', 'slim-seo-schema' ), $label );
				$options[ $id . '.longitude' ] = sprintf( __( '%s (longitude)', 'slim-seo-schema' ), $label );
			} else {
				$options[ $id ] = $label;
			}

			if ( ! empty( $field['fields'] ) ) {
				$options = array_merge( $options, $this->add_fields( $field['fields'], $id, $sub_indent ) );
			}
		}
		return $options;
	}

	private function has_value( $field ) {
		return ! in_array( $field['type'], [ 'heading', 'divider', 'custom_html', 'button' ], true );
	}

	private static function normalize( $id ) {
		return str_replace( '-', '_', $id );
	}
}
