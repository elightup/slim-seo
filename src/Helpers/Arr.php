<?php
namespace SlimSEO\Helpers;

class Arr {
	public static function is_numeric_key( array $arr ): bool {
		return count( array_filter( array_keys( $arr ), 'is_string' ) ) === 0;
	}

	/**
	 * Merge 2 arrays recursively.
	 * The behavior of array_merge_recursive is not the same as array_merge, so we need a fix for that.
	 * This merges only if 2 arrays are both numeric or both associate arrays.
	 *
	 * @see  array_replace_recursive for a similar behavior.
	 * @link https://www.php.net/manual/en/function.array-merge-recursive.php
	 */
	public static function merge_recursive( array $arr1, array $arr2 ): array {
		// Only merge if both arrays are numeric or associate arrays.
		if (
			( self::is_numeric_key( $arr1 ) && ! self::is_numeric_key( $arr2 ) )
			|| ( ! self::is_numeric_key( $arr1 ) && self::is_numeric_key( $arr2 ) )
		) {
			return $arr2;
		}

		$merged = $arr1;

		foreach ( $arr2 as $key => $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$merged[ $key ] = self::merge_recursive( $merged[ $key ], $value );
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}

	/**
	 * Flatten an array.
	 *
	 * @link https://stackoverflow.com/a/1320156/371240
	 *
	 * @param  array $arr Input array.
	 * @return array
	 */
	public static function flatten( $arr ) {
		if ( ! is_array( $arr ) ) {
			return $arr;
		}
		$return = [];
		array_walk_recursive( $arr, function ( $a ) use ( &$return ) {
			$return[] = $a;
		} );
		return $return;
	}

	/**
	 * Convert flatten collection (with dot notation) to multiple dimensional array
	 *
	 * @param  collection $collection Collection to be flatten.
	 * @return array
	 */
	public static function undot( $collection, $output = [] ) {
		$collection = (array) $collection;

		foreach ( $collection as $key => $value ) {
			self::set( $output, $key, $value );

			if ( is_array( $value ) && ! strpos( $key, '.' ) ) {
				$nested         = self::undot( $value );
				$output[ $key ] = $nested;
			}
		}

		return $output;
	}

	/**
	 * Flatten a multi-dimensional associative array with dots.
	 *
	 * @return array
	 */
	public static function dot( $arr, $prepend = '' ) {
		$results = [];

		foreach ( $arr as $key => $value ) {
			if ( is_array( $value ) && ! empty( $value ) ) {
				$results = array_merge( $results, static::dot( $value, $prepend . $key . '.' ) );
			} else {
				$results[ $prepend . $key ] = $value;
			}
		}

		return $results;
	}

	/**
	 * Set array element value with dot notation.
	 */
	public static function set( &$arr, $key, $value ) {
		if ( $key === '' ) {
			$arr = $value;
			return $arr;
		}

		// Do not parse email value.
		if ( is_email( $key ) ) {
			$arr[ $key ] = $value;
			return;
		}

		$keys = explode( '.', $key );

		while ( count( $keys ) > 1 ) { // phpcs:ignore Squiz.PHP.DisallowSizeFunctionsInLoops.Found
			$key = array_shift( $keys );

			// If the key doesn't exist at this depth, we will just create an empty array
			// to hold the next value, allowing us to create the arrays to hold final
			// values at the correct depth. Then we'll keep digging into the array.
			if ( ! isset( $arr[ $key ] ) || ! is_array( $arr[ $key ] ) ) {
				$arr[ $key ] = [];
			}

			$arr =& $arr[ $key ];
		}

		$arr[ array_shift( $keys ) ] = $value;
	}

	/**
	 * Get array element value with dot notation.
	 */
	public static function get( $arr, $key, $default_value = null ) {
		if ( ! $key ) {
			return $arr;
		}

		$keys = explode( '.', $key );
		foreach ( $keys as $key ) {
			if ( isset( $arr[ $key ] ) ) {
				$arr = $arr[ $key ];
			} else {
				return $default_value;
			}
		}

		return $arr;
	}

	/**
	 * Find an element in a multi-dimensional array by key and value.
	 */
	public static function find( $arr, $key, $value, $normalizer = null ) {
		$values = wp_list_pluck( $arr, $key );

		if ( $normalizer ) {
			$value  = $normalizer( $value );
			$values = array_map( $normalizer, $values );
		}

		$index = array_search( $value, $values, true );

		return $index === false ? null : $arr[ $index ];
	}

	public static function find_sub_field( $field, $key, $normalizer = null ) {
		if ( ! $key ) {
			return $field;
		}

		$keys = explode( '.', $key );
		foreach ( $keys as $key ) {
			if ( empty( $field['fields'] ) ) {
				return null;
			}

			$field = self::find( $field['fields'], 'id', $key, $normalizer );
			if ( ! $field ) {
				return null;
			}
		}

		return $field;
	}
}
