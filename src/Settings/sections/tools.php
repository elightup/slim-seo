<h3><?php esc_attr_e( 'Migrate SEO Data', 'slim-seo' ); ?></h3>
<p><?php esc_html_e( 'Use the drop down below to choose which plugin you wish to import SEO data from.', 'slim-seo' ); ?></p>
<strong><?php esc_attr_e( 'Before performing an import, we strongly recommend that you make a backup of your site.', 'slim-seo' ); ?></strong>

<p class="migration-handler">
	<label for="platform"><?php esc_html_e( 'Migrate SEO data from:', 'slim-seo' ); ?></label>
	<select name="platform" id="platform">
		<optgroup value="meta" label="<?php esc_html_e( 'SEO plugins', 'slim-seo' ); ?>">
			<?php $platforms = SlimSEO\Migration\Helper::get_platforms( 'meta' ) ?>
			<?php foreach ( $platforms as $key => $platform ) : ?>
				<option value="<?= esc_attr( $key ); ?>"><?= esc_html( $platform ); ?></option>
			<?php endforeach ?>
		</optgroup>

		<optgroup value="redirection" label="<?php esc_html_e( 'Redirection plugins', 'slim-seo' ); ?>">
			<?php $platforms = SlimSEO\Migration\Helper::get_platforms( 'redirection' ) ?>
			<?php foreach ( $platforms as $key => $platform ) : ?>
				<option value="<?= esc_attr( $key ); ?>"><?= esc_html( $platform ); ?></option>
			<?php endforeach ?>
		</optgroup>
	</select>
	<button type="button" class="button button-primary" id="process"><?php esc_html_e( 'Migrate', 'slim-seo' ); ?></button>
</p>

<div class="migration-status">
	<div id="posts-status"></div>
	<div id="terms-status"></div>
	<div id="redirects-status"></div>
	<div id="done-status"></div>
</div>

<?php do_action( 'slim_seo_tools_tab_content' ); ?>
