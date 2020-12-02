<?php
namespace SlimSEO\Migration;

class RankMathManager extends \RankMath\Replace_Variables\Manager {
	private $post_id;
	private $term_id;

	public function set_post( $post_id ) {
		$this->post_id = $post_id;
	}

	public function set_term( $term_id ) {
		$this->term_id = $term_id;
	}

	public function get_post() {
		$this->post = get_post( $this->post_id );
		return $this->post;
	}

	public function get_term() {
		$term = get_term( $this->term_id );

		return $term->name;
	}

	public function should_we_setup() {
		return true;
	}
}
