<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\SlimSEOHead;
use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\OpenGraph;
use SlimSEO\MetaTags\Robots;
use SlimSEO\MetaTags\TwitterCards;
use SlimSEO\MetaTags\LinkedIn;

class UltimateMember {
	private $head;
	private $description;
	private $open_graph;
	private $twitter_cards;
	private $linkedin;
	private $robots;

	public function __construct(
		SlimSEOHead $slim_seo_head,
		Description $description,
		OpenGraph $open_graph,
		TwitterCards $twitter_cards,
		LinkedIn $linkedin,
		Robots $robots
	) {
		$this->head          = $slim_seo_head;
		$this->description   = $description;
		$this->open_graph    = $open_graph;
		$this->twitter_cards = $twitter_cards;
		$this->linkedin      = $linkedin;
		$this->robots        = $robots;
	}

	public function is_active(): bool {
		return function_exists( 'um_is_core_page' );
	}

	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! um_is_core_page( 'user' ) ) {
			return;
		}

		remove_action( 'wp_head', [ $this->head, 'slim_seo_head' ], 1 );
		remove_filter( 'wp_robots', [ $this->robots, 'modify_robots' ] );
	}
}
