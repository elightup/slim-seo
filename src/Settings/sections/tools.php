<?php
$platforms = SlimSEO\Migration\Helper::get_migration_platforms();
if ( empty( $platforms ) ) {
	return;
}
?>

<div id="tools" class="ss-tab-pane">
	<h3><?php esc_attr_e( 'Migrate SEO Data', 'slim-seo' ); ?></h3>
	<p><?php esc_html_e( 'Use the drop down below to choose which plugin or theme you wish to import SEO data from.', 'slim-seo' ); ?></p>
	<strong><?php esc_attr_e( 'Before performing an import, we strongly recommend that you make a backup of your site.', 'slim-seo' ); ?></strong>

	<p class="migration-handler">
		<label for="platform">Migrate SEO data from:</label>
		<select name="platform" id="platform">
			<?php foreach ( $platforms as $id => $platform ): ?>
				<option value="<?= esc_attr( $id ); ?>">
					<?= esc_html( $platform ); ?>
				</option>
			<?php endforeach ?>
		</select>
		<button type="button" class="button button-primary" id="process">
			<?php _e( 'Migrate', 'slim-seo' ); ?>
		</button>
	</p>

	<div class="migration-status">
		<div id="prepare-migration-status"></div>
		<div id="posts-migration-status"></div>
		<div id="terms-migration-status"></div>
	</div>
</div>