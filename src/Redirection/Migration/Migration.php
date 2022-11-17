<?php
namespace SlimSEO\Redirection\Migration;

use SlimSEO\Redirection\Database\Redirects as DbRedirects;
use SlimSEO\Redirection\Helper;

class Migration {
	protected $db_redirects;

	public function __construct( DbRedirects $db_redirects ) {
		$this->db_redirects = $db_redirects;

		add_action( 'admin_print_styles-settings_page_slim-seo', [ $this, 'enqueue' ] );
		add_filter( 'slim_seo_tools_tab_content', [ $this, 'migrate_redirects_output' ] );
		add_action( 'wp_ajax_ss_migrate_redirects', [ $this, 'migrate_redirects' ] );
	}

	public function enqueue() {
		wp_enqueue_script( 'slim-seo-migrate-redirects', SLIM_SEO_URL . 'js/migrate-redirects.js', [], SLIM_SEO_VER, true );
		wp_localize_script( 'slim-seo-migrate-redirects', 'ssRedirectsMigration', [
			'nonce'          => wp_create_nonce( 'migrate-redirects' ),
			'preProcessText' => __( 'Starting...', 'slim-seo' ),
		] );
	}

	public function migrate_redirects_output() { ?>
		<h3><?php esc_attr_e( 'Migrate Redirects', 'slim-seo' ); ?></h3>

		<p class="redirects-migration-handler">
			<label for="redirection-platform"><?php esc_html_e( 'Migrate Redirects from:', 'slim-seo' ); ?></label>
			<select name="redirection_platform" id="redirection-platform">
				<?php $platforms = Helper::get_migration_platforms(); ?>
				<?php foreach ( $platforms as $key => $platform ) : ?>
					<option value="<?= esc_attr( $key ); ?>"><?= esc_html( $platform ); ?></option>
				<?php endforeach ?>
			</select>
			<button type="button" class="button button-primary" id="redirects-migration-process"><?php esc_html_e( 'Migrate', 'slim-seo' ); ?></button>
		</p>

		<div id="redirects-migration-status"></div>
		<?php
	}

	public function migrate_redirects() {
		$platform     = filter_input( INPUT_GET, 'platform', FILTER_SANITIZE_STRING );
		$replacer     = ReplacerFactory::make( $platform, $this->db_redirects );
		$is_activated = $replacer->is_activated();

		if ( ! $is_activated ) {
			$platforms = Helper::get_migration_platforms();

			// Translators: %s is the plugin name.
			wp_send_json_error( sprintf( __( 'Please activate %s plugin to use this feature. You can deactivate it after migration.', 'slim-seo' ), $platforms[ $platform ] ), 400 );
		}
		$migrated_redirects = $replacer->migrate();

		wp_send_json_success( [
			// Translators: %s is the number of redirects were migrated.
			'message' => $migrated_redirects ? sprintf( __( 'Migrated %s redirects.', 'slim-seo' ), $migrated_redirects ) : __( 'No redirect found!', 'slim-seo' ),
		] );
	}
}
