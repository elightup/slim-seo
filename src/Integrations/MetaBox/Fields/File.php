<?php
namespace SlimSEO\Integrations\MetaBox\Fields;

class File extends Base {
	public static function get_single_value( $value ) {
		// Groups send ID, normal fields send array of file info.
		$value = isset( $value['ID'] ) ? $value['ID'] : $value;

		return wp_get_attachment_url( $value );
	}
}
