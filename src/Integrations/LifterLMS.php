<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Robots;
use SlimSEO\MetaTags\Title;

class LifterLMS {

	private $catalog_page_id;

	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! class_exists( 'LifterLMS' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description', [ $this, 'strip_shortcodes' ] );

		// Catalogs pages like Course and Membership ones: handle title/description/index.
		add_filter( 'slim_seo_meta_title', [ $this, 'catalogs_title' ], 10, 2 );
		add_filter( 'slim_seo_meta_description', [ $this, 'catalogs_description' ], 10, 2 );
		add_filter( 'slim_seo_robots_index', [ $this, 'catalogs_index' ], 10, 2 );
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

	public function catalogs_title( $title, Title $title_obj ) {
		$catalog_page_id = $this->catalog_page_id();
		return $catalog_page_id > 0 ? $title_obj->get_singular_value( $catalog_page_id ) : $title;
	}

	public function catalogs_description( $description, Description $description_obj ) {
		$catalog_page_id = $this->catalog_page_id();
		return $catalog_page_id > 0 ? $description_obj->get_singular_value( $catalog_page_id ) : $description;
	}

	public function catalogs_index( $indexed, Robots $robots ) {
		$catalog_page_id = $this->catalog_page_id();
		if ( $catalog_page_id < 1 ) {
			return $indexed;
		}

		return $robots->get_singular_value( $catalog_page_id ) ? false : $indexed;
	}

	private function is_disabled_context() {
		$pages = [ 'checkout', 'myaccount' ];
		$pages = array_map( 'llms_get_page_id', $pages );
		return is_page( $pages );
	}

	private function catalog_page_id() {
		if ( isset( $this->catalog_page_id ) ) {
			return $this->catalog_page_id;
		}

		$catalog = '';

		if ( is_courses() ) {
			$catalog = 'courses';
		} elseif ( is_memberships() ) {
			$catalog = 'memberships';
		}

		// Cache for future use.
		$this->catalog_page_id = $catalog ? llms_get_page_id( $catalog ) : 0;

		return $this->catalog_page_id;
	}
}
