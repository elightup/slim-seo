<?php
namespace SlimSEO\Settings\PostTypes;

class Loader {
	public function setup() {
		new Api\PostTypes;
		new Api\Data;
		new Api\MetaBox;
		new Api\MetaKeys;
	}
}
