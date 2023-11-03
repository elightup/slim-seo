<?php
namespace SlimSEO\Integrations;

class SenseiLMS {
	public function setup() {
		if ( ! defined( 'SENSEI_LMS_VERSION' ) ) {
			return;
		}

		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
	}

	/**
	 * Don't parse AffiliateWP shortcodes for meta description.
	 * @link https://affiliatewp.com/doc-categories/shortcodes/
	 */
	public function skip_shortcodes( $shortcodes ) {
		$shortcodes = array_merge( $shortcodes, [
			'sensei_courses',
			'sensei_featured_courses',
			'sensei_user_courses',
			'sensei_teachers',
			'sensei_user_messages',
			'sensei_course_page',
			'sensei_course_categories',
			'sensei_unpurchased_courses',
		] );
		return $shortcodes;
	}
}
