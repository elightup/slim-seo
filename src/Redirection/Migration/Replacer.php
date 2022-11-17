<?php
namespace SlimSEO\Redirection\Migration;

use SlimSEO\Redirection\Database\Redirects as DbRedirects;
use SlimSEO\Redirection\Helper;

class Replacer {
	protected $db_redirects;
	protected $redirect_types;

	public function __construct( DbRedirects $db_redirects ) {
		$this->db_redirects   = $db_redirects;
		$this->redirect_types = Helper::redirect_types();
	}

	public function migrate() : int {
		return 0;
	}

	public function is_activated() : bool {
		return true;
	}
}
