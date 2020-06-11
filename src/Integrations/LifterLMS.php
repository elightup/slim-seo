<?php
namespace SlimSEO\Integrations;

class LifterLMS {

	private $catalog_page_id;

	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! class_exists( 'LifterLMS' ) ) {
			return;
		}

		add_filter( 'slim_seo_meta_description', [ $this, 'no_description' ] );

		// Catalogs pages like Course and Membership ones: handle title/description/index.
		add_filter( 'slim_seo_meta_title', [ $this, 'catalogs_title' ], 10, 2 );
		add_filter( 'slim_seo_meta_description', [ $this, 'catalogs_description' ], 10, 2 );
		add_filter( 'slim_seo_robots_index', [ $this, 'catalogs_index' ] );
	}

	public function no_description( $description ) {
		/*
		 * Strip all shortcodes for some LifterLMS pages since they do some logic like setting errors in the session.
		 * Processing these shortcodes might break LifterLMS (like clearing notices in the session).
		 *
		 * @see https://github.com/gocodebox/lifterlms/issues/1181
		 */
		return $this->is_disabled_context() ? strip_shortcodes( $description ) : $description;
	}

	public function catalogs_title( $title, $title_obj ) {
		$catalog_page_id = $this->catalog_page_id();
		return $catalog_page_id > 0 ? $title_obj->get_singular_value( $catalog_page_id ) : $title;
	}

	public function catalogs_description( $description, $description_obj ) {
		$catalog_page_id = $this->catalog_page_id();
		return $catalog_page_id > 0 ? $description_obj->get_singular_value( $catalog_page_id ) : $description;
	}

	public function catalogs_index( $is_indexed ) {
		$catalog_page_id = $this->catalog_page_id();
		if ( $catalog_page_id < 1 ) {
			return $is_indexed;
		}

		$data = get_post_meta( $catalog_page_id, 'slim_seo', true );
		if ( ! empty( $data['noindex'] ) ) {
			return false;
		}

		return $is_indexed;
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
