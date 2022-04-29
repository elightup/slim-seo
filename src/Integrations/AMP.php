<?php
namespace SlimSEO\Integrations;

class AMP {
	private $schema;

	public function __construct( $schema ) {
		$this->schema = $schema;
	}

	public function setup() {
		add_action( 'amp_post_template_footer', [ $this->schema, 'output' ] );

		add_action( 'amp_post_template_head', array( $this, 'remove_default_amp_schema' ), 9 );
	}

	public function remove_default_amp_schema() {
		remove_action( 'amp_post_template_head', 'amp_print_schemaorg_metadata' );
	}
}
