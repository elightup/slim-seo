<?php
namespace SlimSEO\Migration\Sources;

class SquirrlySEO extends Source {
	protected $constant = 'SQ_VERSION';
	private $object = 'post';
	private $post;
	private $term;

	protected function before_migrate_post( $post_id ) {
		$this->post = $this->before_migrate( $post_id );
	}

	protected function before_migrate_term( $term_id ) {
		$this->object = 'term';
		$this->term = $this->before_migrate( $term_id );
	}

	private function before_migrate( $object_id ) {
		global $wpdb;
		$table = $wpdb->prefix . _SQ_DB_;

		$link = get_permalink( $object_id );
		if( 'term' === $this->object ) {
			$link = get_term_link( $object_id );
		}

		$seo = $wpdb->get_var( $wpdb->prepare( "SELECT seo FROM `$table` WHERE `url` = %s", $link ) );
		return \SQ_Classes_ObjController::getDomain( 'SQ_Models_Domain_Sq', maybe_unserialize( $seo ) );
	}

	protected function get_post_title( $post_id ) {
		return empty( $this->post->title ) ? '' : $this->replace_with_slim_seo_variables( $this->post->title );
	}

	protected function get_post_description( $post_id ) {
		return empty( $this->post->description ) ? '' : $this->replace_with_slim_seo_variables( $this->post->description );
	}

	protected function get_post_facebook_image( $post_id ) {
		return $this->post->og_media ?: '';
	}

	protected function get_post_twitter_image( $post_id ) {
		return $this->post->tw_media ?: '';
	}

	protected function get_post_noindex( $post_id ) {
		return (int) $this->post->noindex;
	}

	protected function get_term_title( $post_id ) {
		return empty( $this->term->title ) ? '' : $this->replace_with_slim_seo_variables( $this->term->title );
	}

	protected function get_term_description( $post_id ) {
		return empty( $this->term->description ) ? '' : $this->replace_with_slim_seo_variables( $this->term->description );
	}

	protected function get_term_facebook_image( $post_id ) {
		return $this->term->og_media ?: '';
	}

	protected function get_term_twitter_image( $post_id ) {
		return $this->term->tw_media ?: '';
	}

	protected function get_term_noindex( $post_id ) {
		return (int) $this->term->noindex;
	}

	private function replace_with_slim_seo_variables( string $text ): string {
		$variables = [
			'{{title}}'                => '{{ post.title }}',
			'{{excerpt}}'              => '{{ post.auto_description }}',
			'{{tag}}'                  => '{{ post.tags }}',
			'{{date}}'                 => '{{ post.date }}',
			'{{category}}'             => '{{ post.categories }}',
			'{{modified}}'             => '{{ post.modified_date }}',
			'{{name}}'                 => '{{ author.display_name }}',
			'{{single}}'               => '{{ post_type.singular }}',
			'{{plural}}'               => '{{ post_type.plural }}',
			'{{term_title}}'           => '{{ term.name }}',
			'{{term_description}}'     => '{{ term.description }}',
			'{{category_description}}' => '{{ term.description }}',
			'{{sitename}}'             => '{{ site.title }}',
			'{{sitedesc}}'             => '{{ site.description }}',
			'{{currentmonth}}'         => '{{ current.month }}',
			'{{currentyear}}'          => '{{ current.year }}',
			'{{sep}}'                  => '{{ sep }}',
			'{{page}}'                 => '{{ page }}',

		];

		return strtr( $text, $variables );
	}
}