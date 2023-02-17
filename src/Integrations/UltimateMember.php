<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\OpenGraph;
use SlimSEO\MetaTags\Robots;
use SlimSEO\MetaTags\TwitterCards;

class UltimateMember {
	private $description;
	private $open_graph;
	private $twitter_cards;
	private $robots;

	public function __construct(
		Description $description,
		OpenGraph $open_graph,
		TwitterCards $twitter_cards,
		Robots $robots
	) {
		$this->description   = $description;
		$this->open_graph    = $open_graph;
		$this->twitter_cards = $twitter_cards;
		$this->robots        = $robots;
	}

	public function setup() {
		add_action( 'template_redirect', [ $this, 'process' ] );
	}

	public function process() {
		if ( ! function_exists( 'um_is_core_page' ) || ! um_is_core_page( 'user' ) ) {
			return;
		}

		remove_action( 'wp_head', [ $this->description, 'output' ] );
		remove_action( 'wp_head', [ $this->open_graph, 'output' ] );
		remove_action( 'wp_head', [ $this->twitter_cards, 'output' ] );
		remove_filter( 'wp_robots', [ $this->robots, 'modify_robots' ] );
	}
}
