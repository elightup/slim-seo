<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\OpenGraph;
use SlimSEO\MetaTags\Robots;
use SlimSEO\MetaTags\TwitterCards;
use SlimSEO\MetaTags\LinkedIn;

class UltimateMember {
	private $description;
	private $open_graph;
	private $twitter_cards;
	private $linkedin;
	private $robots;

	public function __construct(
		Description $description,
		OpenGraph $open_graph,
		TwitterCards $twitter_cards,
		LinkedIn $linkedin,
		Robots $robots
	) {
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

		remove_action( 'wp_head', [ $this->description, 'output' ] );
		remove_action( 'wp_head', [ $this->open_graph, 'output' ] );
		remove_action( 'wp_head', [ $this->twitter_cards, 'output' ] );
		remove_action( 'wp_head', [ $this->linkedin, 'output' ] );
		remove_filter( 'wp_robots', [ $this->robots, 'modify_robots' ] );
	}
}
