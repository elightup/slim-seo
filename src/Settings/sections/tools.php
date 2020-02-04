<?php
$platforms = $this->migration_platforms();
if ( empty( $platforms ) ) {
	return;
}
?>

<div id="tools" class="ss-tab-pane">
	<h3><?php esc_attr_e( 'Migrate SEO Data', 'slim-seo' ); ?></h3>
	<p><?php esc_html_e( 'Use the drop down below to choose which plugin or theme you wish to import SEO data from.', 'slim-seo' ); ?></p>
	<strong><?php esc_attr_e( 'Before performing an import, we strongly recommend that you make a backup of your site.', 'slim-seo' ); ?></strong>
	<form method="post" action="">
		<p>
			<label for="platform">Migrate SEO data from:</label>
			<select name="platform" id="platform">
				<?php foreach ( $platforms as $id => $platform ): ?>
					<option value="<?php echo esc_attr( $id ); ?>">
						<?php echo esc_html( $platform ); ?>
					</option>
				<?php endforeach ?>
			</select>
			<a href="#" class="button button-primary" id="process"
				data-nonce="<?php echo wp_create_nonce( 'migrate' ); ?>"
				data-done_text="<?php esc_attr_e( 'Done!', 'slim-seo' ); ?>"
				data-pre-process="<?php esc_attr_e( 'Starting...', 'slim-seo' ); ?>"
			>
				<?php _e( 'Migrate', 'slim-seo' ); ?>
			</a>
		</p>
	</form>

	<div class="migration-status">
		<div id="posts-migration-status"></div>
		<div id="terms-migration-status"></div>
	</div>
</div>