<?php
namespace SlimSEO\Migration\Sources;

abstract class Source {
	protected $constant = '';

	public function is_activated(): bool {
		return defined( $this->constant );
	}

	public function migrate_post( $post_id ) {
		$this->before_migrate_post( $post_id );

		$settings = array_filter( [
			'title'          => $this->get_post_title( $post_id ),
			'description'    => $this->get_post_description( $post_id ),
			'facebook_image' => $this->get_post_facebook_image( $post_id ),
			'twitter_image'  => $this->get_post_twitter_image( $post_id ),
			'noindex'        => $this->get_post_noindex( $post_id ),
		] );
		if ( $settings ) {
			update_post_meta( $post_id, 'slim_seo', $settings );
		}
	}

	public function migrate_term( $term_id ) {
		$this->before_migrate_term( $term_id );

		$settings = [
			'title'          => $this->get_term_title( $term_id ),
			'description'    => $this->get_term_description( $term_id ),
			'facebook_image' => $this->get_term_facebook_image( $term_id ),
			'twitter_image'  => $this->get_term_twitter_image( $term_id ),
			'noindex'        => $this->get_term_noindex( $term_id ),
		];
		$settings = array_filter( $settings );
		if ( $settings ) {
			update_term_meta( $term_id, 'slim_seo', $settings );
		}
	}

	public function migrate_redirects() {
	}

	// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.Found

	protected function before_migrate_post( $post_id ) {
	}

	protected function before_migrate_term( $term_id ) {
	}

	protected function get_post_title( $post_id ) {
		return '';
	}

	protected function get_post_description( $post_id ) {
		return '';
	}

	protected function get_post_facebook_image( $post_id ) {
		return '';
	}

	protected function get_post_twitter_image( $post_id ) {
		return '';
	}

	protected function get_post_noindex( $post_id ) {
		return 0;
	}

	protected function get_term_title( $term_id ) {
		return '';
	}

	protected function get_term_description( $term_id ) {
		return '';
	}

	protected function get_term_facebook_image( $term_id ) {
		return '';
	}

	protected function get_term_twitter_image( $term_id ) {
		return '';
	}

	protected function get_term_noindex( $term_id ) {
		return 0;
	}
}
