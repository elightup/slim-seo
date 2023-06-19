<?php
namespace SlimSEO\Migration\Sources;

class SEOPress extends Source {
    protected $constant = 'SEOPRESS_VERSION';
	private $context;

	protected function before_migrate_post( $post_id ) {
		$this->context = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( $post_id )->getContext();
	}

	protected function get_post_title( $post_id ) {
		return seopress_get_service( 'TitleMeta' )->getValue( $this->context );
	}

	protected function get_post_description( $post_id ) {
		return seopress_get_service( 'DescriptionMeta' )->getValue( $this->context );
	}

	protected function get_post_facebook_image( $post_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		return $social['og']['image'] ?? null;
	}

	protected function get_post_twitter_image( $post_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		return $social['twitter']['image'] ?? null;
	}

	protected function get_post_noindex( $post_id ) {
		$robots = seopress_get_service( 'RobotMeta' )->getValue( $this->context );
		return intval( ! empty( $robots['noindex'] ) );
	}

	protected function before_migrate_term( $term_id ) {
		$this->context = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( $term_id, [ 'type' => 'term' ] )->getContext();
	}

	protected function get_term_title( $term_id ) {
		return seopress_get_service( 'TitleMeta' )->getValue( $this->context );
	}

	protected function get_term_description( $term_id ) {
		return seopress_get_service( 'DescriptionMeta' )->getValue( $this->context );
	}

	protected function get_term_facebook_image( $term_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		return $social['og']['image'] ?? null;
	}

	protected function get_term_twitter_image( $term_id ) {
		$social = seopress_get_service( 'SocialMeta' )->getValue( $this->context );
		return $social['twitter']['image'] ?? null;
	}

	protected function get_term_noindex( $term_id ) {
		$robots = seopress_get_service( 'RobotMeta' )->getValue( $this->context );
		return intval( ! empty( $robots['noindex'] ) );
	}
}
