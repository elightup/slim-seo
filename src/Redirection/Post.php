<?php
namespace SlimSEO\Redirection;

use SlimSEO\Helpers\Assets;
use SlimSEO\Redirection\Database\Redirects as DbRedirects;
use eLightUp\SlimSEO\Common\Assets as CommonAssets;
use eLightUp\SlimSEO\Common\Helpers\Data as CommonHelpersData;

class Post {
	protected $db_redirects;

	public function __construct( DbRedirects $db_redirects ) {
		$this->db_redirects = $db_redirects;

		add_action( 'init', [ $this, 'setup' ] );
	}

	public function setup(): void {
		add_action( 'slim_seo_meta_box_enqueue', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_meta_box_tabs', [ $this, 'tabs' ], 100 );
		add_filter( 'slim_seo_meta_box_panels', [ $this, 'panels' ], 100 );
		add_action( 'save_post', [ $this, 'save' ] );

		$post_types = array_keys( CommonHelpersData::get_post_types() );

		foreach ( $post_types as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", [ $this, 'columns' ], 9999 );
			add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'column_render' ], 9999, 2 );
		}
	}

	public function enqueue(): void {
		CommonAssets::enqueue_css( 'components' );
		CommonAssets::enqueue_css( 'settings' );

		wp_enqueue_style( 'slim-seo-redirection', SLIM_SEO_URL . 'css/redirection.css', [ 'wp-components' ], filemtime( SLIM_SEO_DIR . 'css/redirection.css' ) );

		Assets::enqueue_build_js( 'redirection-post', 'SSRedirection', [
			'rest'             => untrailingslashit( rest_url() ),
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'redirectTypes'    => Helper::redirect_types(),
			'conditionOptions' => Helper::condition_options(),
			'redirect'         => array_merge(
				Helper::default_redirect(),
				$this->db_redirects->find_by_from_url( Helper::normalize_url( get_permalink(), false ) )
			),
		] );
	}

	public function tabs( array $tabs ): array {
		$tabs['redirection'] = esc_html__( 'Redirection', 'slim-seo' );

		return $tabs;
	}

	public function panels( array $panels ): array {
		ob_start();

		wp_nonce_field( 'save', 'ss_redirection_nonce' );
		?>

		<div id="ss-redirection"></div>

		<?php
		$panels['redirection'] = ob_get_clean();

		return $panels;
	}

	public function save( int $post_id ): void {
		if ( ! check_ajax_referer( 'save', 'ss_redirection_nonce', false ) || empty( $_POST ) ) {
			return;
		}

		$redirect = isset( $_POST['slim_seo_redirect'] ) ? wp_unslash( $_POST['slim_seo_redirect'] ) : []; // phpcs:ignore

		if ( empty( $redirect ) ) {
			return;
		}

		$from           = get_permalink( $post_id );
		$old_redirect   = $this->db_redirects->find_by_from_url( Helper::normalize_url( $from, false ) );
		$redirect['id'] = $old_redirect['id'] ?? 0;

		if ( 410 === intval( $redirect['type'] ) ) {
			$redirect['to'] = '';
		} else {
			if ( empty( $redirect['to'] ) ) {
				if ( ! empty( $redirect['id'] ) ) {
					$this->db_redirects->delete( [ $redirect['id'] ] );
				}

				return;
			}
		}

		$this->db_redirects->update( array_merge(
			[
				'from'             => $from,
				'condition'        => 'exact-match',
				'enable'           => 0,
				'ignoreParameters' => 0,
			],
			$redirect
		) );
	}

	public function columns( array $columns ): array {
		$columns['redirect'] = esc_html__( 'Redirected', 'slim-seo' );

		return $columns;
	}

	public function column_render( string $column, int $post_id ): void {
		if ( 'redirect' !== $column ) {
			return;
		}

		$redirect = $this->db_redirects->find_by_from_url( Helper::normalize_url( get_permalink( $post_id ), false ) );

		if ( empty( $redirect ) ) {
			return;
		}

		$to = $redirect['to'];
		$to = Helper::url_valid( $to ) ? $to : Helper::home_url( $to );

		echo ! empty( $redirect ) && ! empty( $redirect['enable'] ) ? '<span class="ss-success" title="' . esc_url( $to ) . '"></span>' : '<span class="ss-danger"></span>';
	}
}
