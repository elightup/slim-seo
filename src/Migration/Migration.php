<?php
namespace SlimSEO\Migration;

class Migration {

	/**
	 * Number of posts being processed in 1 call
	 * @var int
	 */
	public $threshold = 10;

	public function setup() {
		add_action( 'wp_ajax_migrate_yoast', [ $this, 'handle_ajax' ] );
	}

	public function handle_ajax() {
		check_ajax_referer( 'migrate' );

		$restart = isset( $_POST['restart'] ) ? intval( $_POST['restart'] ) : 0;

		// If restart the process, reset session and send "continue" command
		session_start();
		if ( $restart ) {

			$_SESSION['processed'] = 0;

			wp_send_json_success( array(
				'message' => '',
				'type'    => 'continue',
			) );
		}

		$posts = $this->get_posts();
		$processed = $_SESSION['processed'] + count( $posts ) - $this->threshold;
		if ( ! $posts ) {
			wp_send_json_success( array(
				'message' => '',
				'type'    => 'done',
			) );
		}
		foreach( $posts as $post_id ) {
			$this->migrate( $post_id );
		}

		wp_send_json_success( array(
			'message' => sprintf( __( 'Processed %d posts', 'slim-seo' ), $processed ),
			'posts'   => $processed,
			'type'    => 'continue',
		) );
	}

	private function migrate( $post_id ) {
		$replacer = ReplacerFactory::make( 'yoast' );
		$replacer->replace( $post_id );
	}

	private function get_posts() {
		session_start();

		$offset                = isset( $_SESSION['processed'] ) ? $_SESSION['processed'] : 0;
		$_SESSION['processed'] = $_SESSION['processed'] + $this->threshold;

		$posts = new \WP_Query( [
			'post_type'      => 'post',
			'post_status'    => ['publish', 'draft'],
			'posts_per_page' => $this->threshold,
			'no_found_rows'  => true,
			'fields'         => 'ids',
			'offset'         => $offset,
		] );

		if( ! $posts->have_posts() ) {
			return false;
		}
		return $posts->posts;
	}
}

/*class AIOSEO implements Replacer {
	use GetVariableValues;
	public function replace( $value ) {
		$keys = [
			'%%site_title%%' => 'site_title',
			'%%post_title%%' => 'post_title',
		];
		$replacements = str_replace( $keys, $this->get_variable_values() );
		return str_replace( $replacements, $value );
	}
}

trait GetVariableValues {
	public function get_variable_values() {
		return [
			'site_title'       => get_bloginfo( 'name' ),
			'site_description' => get_bloginfo( 'description' ),
			'post_title'       => get_the_title(),
		];
	}
}
*/