<?php
namespace SlimSEO\Integrations;

class LifterLMS {
	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! class_exists( 'LifterLMS' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description', [ $this, 'strip_shortcodes' ] );
	}

	/**
	 * Strip all shortcodes for some LifterLMS pages since they do some logic like setting errors in the session.
	 * Processing these shortcodes might break LifterLMS (like clearing notices in the session).
	 *
	 * @see https://github.com/gocodebox/lifterlms/issues/1181
	 */
	public function strip_shortcodes( $description ) {
		return $this->is_disabled_context() ? strip_shortcodes( $description ) : $description;
	}

	private function is_disabled_context() {
		$pages = [ 'checkout', 'myaccount' ];
		$pages = array_map( 'llms_get_page_id', $pages );
		return is_page( $pages );
	}
}
