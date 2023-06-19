<?php
namespace SlimSEO\Migration\Sources;

abstract class Source {
	protected $constant = '';

	public function migrate_post( $post_id ) {
		$this->before_migrate_post( $post_id );

		$seo_settings = [
			'title'          => $this->get_post_title( $post_id ),
			'description'    => $this->get_post_description( $post_id ),
			'facebook_image' => $this->get_post_facebook_image( $post_id ),
			'twitter_image'  => $this->get_post_twitter_image( $post_id ),
			'noindex'        => $this->get_post_noindex( $post_id ),
		];
		$seo_settings = array_filter( $seo_settings );
		if ( $seo_settings ) {
			update_post_meta( $post_id, 'slim_seo', $seo_settings );
		}
	}

	public function migrate_term( $term_id ) {
		$this->before_migrate_term( $term_id );

		$seo_settings = [
			'title'          => $this->get_term_title( $term_id ),
			'description'    => $this->get_term_description( $term_id ),
			'facebook_image' => $this->get_term_facebook_image( $term_id ),
			'twitter_image'  => $this->get_term_twitter_image( $term_id ),
			'noindex'        => $this->get_term_noindex( $term_id ),
		];
		$seo_settings = array_filter( $seo_settings );
		if ( $seo_settings ) {
			update_term_meta( $term_id, 'slim_seo', $seo_settings );
		}
	}

	protected function before_migrate_post( $post_id ) {
	}

	protected function before_migrate_term( $term_id ) {
	}

	protected function get_post_title( $post_id ) {
		return null;
	}

	protected function get_post_description( $post_id ) {
		return null;
	}

	protected function get_post_facebook_image( $post_id ) {
		return null;
	}

	protected function get_post_twitter_image( $post_id ) {
		return null;
	}

	protected function get_post_noindex( $post_id ) {
		return null;
	}

	protected function get_term_title( $term_id ) {
		return null;
	}

	protected function get_term_description( $term_id ) {
		return null;
	}

	protected function get_term_facebook_image( $term_id ) {
		return null;
	}

	protected function get_term_twitter_image( $term_id ) {
		return null;
	}

	protected function get_term_noindex( $term_id ) {
		return null;
	}

	public function migrate_redirects() {
		return null;
	}

	public function is_activated(): bool {
		return defined( $this->constant );
	}
}
