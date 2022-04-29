<?php
namespace SlimSEO\Migration;

class SEOPress extends Replacer {
	private $context;
	private $variables;
	private $replacements;

	public function before_replace_post( $post_id ) {
		$this->context = [
			'post'           => get_post( $post_id ),
			'product'        => null,
			'term_id'        => null,
			'is_single'      => true,
			'is_home'        => false,
			'is_product'     => false,
			'is_archive'     => false,
			'is_category'    => false,
			'is_author'      => false,
			'is_404'         => false,
			'has_category'   => false,
			'has_tag'        => false,
			'paged'          => 0,
			'schemas_manual' => [],
		];
	}

	public function get_post_title( $post_id ) {
		return seopress_get_service( 'TitleMeta' )->getValue( $this->context );
	}

	public function get_post_description( $post_id ) {
		return seopress_get_service( 'DescriptionMeta' )->getValue( $this->context );
	}

	public function get_post_facebook_image( $post_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		$og     = isset( $social['og'] ) ? $social['og'] : [];
		return isset( $og['image'] ) ? $og['image'] : null;
	}

	public function get_post_twitter_image( $post_id ) {
		$social  = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		$twitter = isset( $social['twitter'] ) ? $social['twitter'] : [];
		return isset( $twitter['image'] ) ? $twitter['image'] : null;
	}

	protected function get_post_noindex( $post_id ) {
		$robots = seopress_get_service( 'RobotMeta' )->getValue( $this->context );
		return intval( ! empty( $robots['noindex'] ) );
	}

	public function before_replace_term( $term_id ) {
		// @codingStandardsIgnoreLine.
		$variables          = apply_filters( 'seopress_dyn_variables_fn', [] );
		$this->variables    = $variables['seopress_titles_template_variables_array'];
		$this->replacements = $variables['seopress_titles_template_replace_array'];
	}

	public function get_term_title( $term_id ) {
		$title = get_term_meta( $term_id, '_seopress_titles_title', true );
		return $title ? str_replace( $this->variables, $this->replacements, $title ) : '';
	}

	public function get_term_description( $term_id ) {
		$description = get_term_meta( $term_id, '_seopress_titles_desc', true );
		return $description ? str_replace( $this->variables, $this->replacements, $description ) : '';
	}

	public function get_term_facebook_image( $term_id ) {
		return get_term_meta( $term_id, '_seopress_social_fb_img', true );
	}

	public function get_term_twitter_image( $term_id ) {
		return get_term_meta( $term_id, '_seopress_social_twitter_img', true );
	}

	public function get_term_noindex( $term_id ) {
		$value = get_term_meta( $term_id, '_seopress_robots_index', true );
		return intval( $value === 'yes' );
	}

	public function is_activated() {
		return defined( 'SEOPRESS_VERSION' );
	}
}
