<?php
namespace SlimSEO\Settings\PostTypes\Api;

class MetaKeys extends Base {
	public function register_routes() {
		register_rest_route( 'slim-seo-post-types', 'meta_keys', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_meta_keys' ],
			'permission_callback' => [ $this, 'has_permission' ],
		] );
	}

	public function get_meta_keys() {
		global $wpdb;
		$meta_keys = $wpdb->get_col( "SELECT DISTINCT meta_key FROM $wpdb->postmeta ORDER BY meta_key" );
		$meta_keys = $this->exclude_defaults( $meta_keys );
		$options   = [];
		foreach ( $meta_keys as $key => $value ) {
			$options[] = [
				'value' => $value,
				'label' => $value,
			];
		}

		return $options;
	}

	public function exclude_defaults( $meta_keys ) {
		$default = [
			'_edit_last',
			'_edit_lock',
			'_encloseme',
			'_event_name',
			'_generate-full-width-content',
			'_icon',
			'_menu_item_classes',
			'_menu_item_menu_item_parent',
			'_menu_item_object',
			'_menu_item_object_id',
			'_menu_item_target',
			'_menu_item_type',
			'_menu_item_url',
			'_menu_item_xfn',
			'_pingme',
			'_primary_term_category',
			'_thumbnail_id',
			'_wp_attached_file',
			'_wp_attachment_image_alt',
			'_wp_attachment_metadata',
			'_wp_desired_post_slug',
			'_wp_old_date',
			'_wp_page_template',
			'_wp_suggested_privacy_policy_content',
			'_wp_trash_meta_status',
			'_wp_trash_meta_time',
			'action_priority',
			'action_type',
			'data',
			'event_name',
			'fields',
			'icon',
			'mb_testimonials_columns',
			'mb_testimonials_display_arrows',
			'mb_testimonials_display_dots',
			'mb_testimonials_dots',
			'mb_testimonials_enable_slider',
			'mb_testimonials_group',
			'mb_testimonials_image_width',
			'mb_testimonials_number_slider_items',
			'mb_testimonials_number_slider_items_desktop',
			'mb_testimonials_number_slider_items_mobile',
			'mb_testimonials_style',
			'mb_testimonials_transition',
			'mb_views_custom_css',
			'mbfp_count',
			'mbfp_posts',
			'menu-icons',
			'meta_box',
			'mobile',
			'mode',
			'position',
			'relationship',
			'settings',
			'singular_locations',
			'slim_seo',
			'type',
		];
		return array_values( array_diff( $meta_keys, $default ) );
	}
}
