<?php
namespace SlimSEO\Integrations\ACF;

use SlimSEO\Renderer\ActiveVariable;
use SlimSEO\Renderer\CloneableProps;

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

		return $this->get_data( $field );
	}

	private function repeater_deep_get( $value, $field ) {
		$variable  = ActiveVariable::get();
		$field_ids = explode( '.', $variable );

		// phpcs:ignore
		array_shift( $field_ids ); // Remove 'acf.' prefix.
		array_shift( $field_ids ); // Remove repeater name

		if ( empty( $field_ids[0] ) ) {
			return $value;
		}

		$key       = $field_ids[0];
		$new_value = wp_list_pluck( $value, $key );
		$new_value = CloneableProps::empty() ? reset( $new_value ) : $new_value;

		// phpcs:ignore
		array_shift( $field_ids );

		if ( ! empty( $field_ids[0] ) ) {
			$new_value = $this->repeater_deep_get_children( $field_ids, $new_value );
		}

		$value[ $key ] = $new_value;

		return $value;
	}

	private function repeater_deep_get_children( $field_ids, $value ) {
		$child_key = $field_ids[0] ?? '';

		if ( $child_key && ! isset( $value[ $child_key ] ) ) {
			$child_value = [];

			foreach ( $value as $element ) {
				if ( is_array( $element ) ) {
					$temp_arr    = wp_list_pluck( $element, $child_key );
					$temp_arr    = array_filter( $temp_arr );
					$child_value = array_merge( $child_value, $temp_arr );
				}
			}

			// phpcs:ignore
			array_shift( $field_ids );

			if ( ! empty( $field_ids[0] ) ) {
				$child_value = $this->repeater_deep_get_children( $field_ids, $child_value );
			}

			if ( ! empty( $child_value ) ) {
				$value[ $child_key ] = $child_value;
			}
		}

		return $value;
	}

	private function layout_deep_get( $value, $field ) {
		$variable  = ActiveVariable::get();
		$field_ids = explode( '.', $variable );

		// phpcs:ignore
		array_shift( $field_ids ); // Remove 'acf.' prefix.
		array_shift( $field_ids ); // Remove flexible content name

		if ( empty( $field_ids[0] ) ) {
			return $value;
		}

		$layout_key = $field_ids[0];
		$fields     = [];

		foreach ( $value as $data ) {
			if ( $layout_key === ( $data['acf_fc_layout'] ?? '' ) ) {
				unset( $data['acf_fc_layout'] );

				$fields[] = $data;
			}
		}

		if ( empty( $field_ids[1] ) ) {
			return $value;
		}

		$key       = $field_ids[1];
		$new_value = wp_list_pluck( $fields, $key );
		$new_value = CloneableProps::empty() ? reset( $new_value ) : $new_value;

		if ( isset( $value[ $layout_key ] ) ) {
			$value[ $layout_key ][ $key ] = $new_value;
		} else {
			$value[ $layout_key ] = [
				$key => $new_value,
			];
		}

		return $value;
	}

	private function get_data( $field ) {
		if ( ! in_array( $field['type'], [ 'group', 'repeater', 'flexible_content' ]  ) ) {
			return $this->parse_normal_field_value( $field['value'], $field );
		}
		$value = $this->parse_group_value( $field['value'], $field );

		// if ( empty( $field['sub_fields'] ) && empty( $field['layouts'] ) ) {

		// 	return $this->parse_normal_field_value( $value, $field );
		// }
		// if ( 'repeater' === $field['type'] ) {
		// 	write_log( 'var '. print_r( 'repeater here', true ) );
		// }
		// if ( in_array( $field['type'], [ 'repeater', 'flexible_content' ] ) ) {
		// 	if ( is_array( $value ) ) {
		// 		// $repeater_field = $field;

		// 		// $repeater_field['type'] = 'group';

		// 		// foreach ( $value as $key => $data ) {
		// 		// 	$value[ $key ] = $this->parse_field_value( $data, $repeater_field );
		// 		// }

		// 		// $value = $this->repeater_deep_get( $value, $field );
		// 		$value = (array) $value;
		// 		$value = reset( $value );
		// 	}
		
		// } else {

		// 	$value = $this->parse_group_value( $value, $field );
		// }

		return $value;
	}

	private function parse_group_value( $value, $field ) {

		if ( $field['type'] === 'repeater' ) {
			$value = (array) $value;
			$value = reset( $value );
		} elseif ( $field['type'] === 'flexible_content' ) {
			$value = $this->parse_flexible_value( $value, $field );
		} else {
			$value = $this->parse_group_clone_value( $value, $field );
		}

		return $value;
	}

	private function parse_group_clone_value( $value, $field ) {
		foreach ( $field['sub_fields'] as $child ) {
			if ( ! isset( $value[ $child['name'] ] ) ) {
				continue;
			}

			$child_value = $value[ $child['name'] ];

			if ( ! empty( $child['sub_fields'] ) ) {
				$child_value = $this->parse_group_value( $child_value, $child );
			} else {
				$child_value = $this->parse_normal_field_value( $child_value, $child );
			}

			$value[ $child['name'] ] = $child_value;
		}

		return $value;
	}

	private function parse_flexible_value( $value, $field ) {
		write_log( 'parse_flexible_valuevalue '. print_r( $value, true ) );

		// return $value;
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
