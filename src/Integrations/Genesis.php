<?php
namespace SlimSEO\Integrations;

class Genesis {
	public function setup() {
		add_action( 'template_redirect', [ $this, 'disable_genesis_schema' ] );
	}

	/**
	 * @link https://wordpress.org/plugins/disable-genesis-schema/
	 * @copyright Bill Erickson <bill@billerickson.net>
	 */
	public function disable_genesis_schema() {
		$elements = array(
			'head',
			'body',
			'site-header',
			'site-title',
			'site-description',
			'breadcrumb',
			'breadcrumb-link-wrap',
			'breadcrumb-link-wrap-meta',
			'breadcrumb-link',
			'breadcrumb-link-text-wrap',
			'search-form',
			'search-form-meta',
			'search-form-input',
			'nav-primary',
			'nav-secondary',
			'nav-header',
			'nav-link-wrap',
			'nav-link',
			'entry',
			'entry-image',
			'entry-image-widget',
			'entry-image-grid-loop',
			'entry-author',
			'entry-author-link',
			'entry-author-name',
			'entry-time',
			'entry-modified-time',
			'entry-title',
			'entry-content',
			'comment',
			'comment-author',
			'comment-author-link',
			'comment-time',
			'comment-time-link',
			'comment-content',
			'author-box',
			'sidebar-primary',
			'sidebar-secondary',
			'site-footer',
		);

		foreach( $elements as $element ) {
			add_filter( 'genesis_attr_' . $element, [ $this, 'remove_genesis_schema_attributes' ], 20 );
		}
	}

	public function remove_genesis_schema_attributes( $attr ) {
		unset( $attr['itemprop'], $attr['itemtype'], $attr['itemscope'] );
		return $attr;
	}
}