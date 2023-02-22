<?php
namespace SlimSEO\Integrations;

use SlimSEO\MetaTags\OpenGraph;
use SlimSEO\MetaTags\TwitterCards;
use SlimSEO\Schema\Manager;

class WebStories {
	private $open_graph;
	private $twitter_cards;
	private $schema;

	public function __construct( OpenGraph $open_graph, TwitterCards $twitter_cards, Manager $schema ) {
		$this->open_graph    = $open_graph;
		$this->twitter_cards = $twitter_cards;
		$this->schema        = $schema;
	}

	public function setup(): void {
		if ( ! defined( 'WEBSTORIES_VERSION' ) ) {
			return;
		}

		// Use priority 20 to make sure all Web Stories hooks are registered.
		add_action( 'init', [ $this, 'remove_web_stories_meta_output' ], 20 );

		add_action( 'web_stories_story_head', [ $this, 'output' ] );
	}

	public function remove_web_stories_meta_output(): void {
		$discovery = \Google\Web_Stories\Services::get( 'discovery' );
		remove_action( 'web_stories_story_head', [ $discovery, 'print_schemaorg_metadata' ] );
		remove_action( 'web_stories_story_head', [ $discovery, 'print_open_graph_metadata' ] );
		remove_action( 'web_stories_story_head', [ $discovery, 'print_twitter_metadata' ] );
	}

	public function output(): void {
		$this->open_graph->output();
		$this->twitter_cards->output();
		$this->schema->output();
	}
}
