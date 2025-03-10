<?php
namespace SlimSEO\Integrations;

class SenseiLMS {
	public function is_active(): bool {
		return defined( 'SENSEI_LMS_VERSION' );
	}

	public function setup() {
		add_action( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
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
