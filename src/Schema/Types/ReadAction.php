<?php
namespace SlimSEO\Schema\Types;

class ReadAction extends Base {
	public function generate() {
		return [
			'@type'  => 'ReadAction',
			'target' => $this->url,
		];
	}
}
