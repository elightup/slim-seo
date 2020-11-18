<?php
namespace SlimSEO\Integrations;

class WebStories {
	private $open_graph;
	private $twitter_cards;
	private $schema;

	public function __construct( $open_graph, $twitter_cards, $schema ) {
		$this->open_graph    = $open_graph;
		$this->twitter_cards = $twitter_cards;
		$this->schema        = $schema;
	}

	public function setup() {
		if ( ! defined( 'WEBSTORIES_VERSION' ) ) {
			return;
		}

		// Use priority 20 to make sure all Web Stories hooks are registered.
		add_action( 'init', [ $this, 'remove_web_stories_meta_output' ], 20 );

		add_action( 'web_stories_story_head', [ $this, 'output' ] );
	}

	public function remove_web_stories_meta_output() {
		$instance = \Google\Web_Stories\get_plugin_instance()->discovery;
		remove_action( 'web_stories_story_head', [ $instance, 'print_schemaorg_metadata' ] );
		remove_action( 'web_stories_story_head', [ $instance, 'print_open_graph_metadata' ] );
		remove_action( 'web_stories_story_head', [ $instance, 'print_twitter_metadata' ] );
	}

	public function output() {
		$this->open_graph->output();
		$this->twitter_cards->output();
		$this->schema->output();
	}
}