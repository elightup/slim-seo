<?php
namespace SlimSEO\Integrations\ACF;

class Renderer {
	protected $field_objects;

	public function __construct( array $field_objects ) {
		$this->field_objects = $field_objects;
	}

	public function __isset( $name ) {
		return true;
	}

	public function __get( $name ) {
		$field = $this->field_objects[ $name ] ?: null;

		if ( ! $field ) {
			return null;
		}

		return $this->parse_field_value( $field['value'], $field );
	}

	private function parse_field_value( $value, $field ) {
		if ( empty( $field['sub_fields'] ) && empty( $field['layouts'] ) ) {
			return $this->parse_normal_field_value( $field['value'], $field );
		}

		if ( 'repeater' === $field['type'] ) {
			$value = (array) $value;
			$value = reset( $value );
			$field['type'] = 'group';

			$value = $this->parse_field_value( $value, $field );
		} elseif ( 'flexible_content' === $field['type'] ) {
			$return_value = [];

			// Flexible content can have multi blocks, each block can have multi values, we need to get first value in each block
			foreach ( $value as $data ) {
				if( ! array_key_exists( $data['acf_fc_layout'], $return_value ) ) {
					$return_value[ $data['acf_fc_layout'] ] = $data;
				}
			}

			foreach ( $return_value as $key => $data ) {
				$layout_field = [];

				foreach ( $field['layouts'] as $layout_data ) {
					if ( $data['acf_fc_layout'] === $layout_data['name'] ) {
						$layout_field = $layout_data;
						break;
					}
				}

				if ( empty( $layout_field ) ) {
					continue;
				}
				$layout_field['type'] = $layout_field['type'] ?? 'group';
				$return_value[ $key ] = $this->parse_field_value( $data, $layout_field );
			}

			$value = $return_value;
		} else {
			$value = $this->parse_group_value( $value, $field );
		}

		return $value;
	}

	private function parse_group_value( $value, $field ) {
		foreach ( $field['sub_fields'] as $child ) {
			if ( ! isset( $value[ $child['name'] ] ) ) {
				continue;
			}

			$child_value = $value[ $child['name'] ];

			if ( ! empty( $child['sub_fields'] ) || ! empty( $child['layouts'] ) ) {
				$child_value = $this->parse_field_value( $child_value, $child );
			} else {
				$child_value = $this->parse_normal_field_value( $child_value, $child );
			}

			$value[ $child['name'] ] = $child_value;
		}

		return $value;
	}

	private function parse_normal_field_value( $value, $field ) {
		switch ( $field['type'] ) {
			case 'select':
			case 'checkbox':
				$field_object = new Fields\Choice( $field, $value );
				break;
			case 'image':
			case 'file':
				$field_object = new Fields\File( $field, $value );
				break;
			case 'link':
				$field_object = new Fields\Link( $field, $value );
				break;
			case 'post_object':
				$field_object = new Fields\Post( $field, $value );
				break;
			case 'relationship':
				$field_object = new Fields\Relationship( $field, $value );
				break;
			case 'taxonomy':
				$field_object = new Fields\Taxonomy( $field, $value );
				break;
			case 'user':
				$field_object = new Fields\User( $field, $value );
				break;
			default:
				$field_object = new Fields\Base( $field, $value );
				break;
		}

		return $field_object->get_value();
	}
}
