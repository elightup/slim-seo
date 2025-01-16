<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\Hook;
use SlimSEO\MetaTags\Robots;

class UltimateMember {
	private $hook;
	private $robots;

	public function __construct(
		Hook $hook,
		Robots $robots
	) {
		$this->hook          = $hook;
		$this->robots        = $robots;
	}

	public function is_active(): bool {
		return function_exists( 'um_is_core_page' );
	}

	public function setup(): void {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process(): void {
		if ( ! um_is_core_page( 'user' ) ) {
			return;
		}

		$this->hook->remove();
		remove_filter( 'wp_robots', [ $this->robots, 'modify_robots' ] );
	}
}
