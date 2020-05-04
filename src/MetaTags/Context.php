<?php
namespace SlimSEO\MetaTags;

trait Context {
	public function get_value() {
		$value = '';

		if ( is_front_page() ) {
			$value = is_page() ? $this->get_singular_value() : $this->get_home_value();
		} elseif ( is_post_type_archive() ) {
			$value = $this->get_post_type_archive_value();
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$value = $this->get_term_value();
		} elseif ( is_home() || is_singular() ) {
			$value = $this->get_singular_value();
		} elseif ( is_author() ) {
			$value = $this->get_author_value();
		}

		return $value;
	}

	private function get_home_value() {
		return null;
	}

	private function get_singular_value() {
		return null;
	}

	private function get_post_type_archive_value() {
		return null;
	}

	private function get_term_value() {
		return null;
	}

	private function get_author_value() {
		return null;
	}
}