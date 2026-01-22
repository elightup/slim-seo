<?php // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals,WordPress.WP.GlobalVariablesOverride ?>

<?php defined( 'ABSPATH' ) || die ?>

<h3><?php esc_attr_e( 'Edit robots.txt', 'slim-seo' ); ?></h3>

<div id="ss-robots"></div>

<h3><?php esc_attr_e( 'Migrate SEO Data', 'slim-seo' ); ?></h3>
<p><?php esc_html_e( 'Use the drop down below to choose which plugin you wish to import SEO data from.', 'slim-seo' ); ?></p>
<p><strong><?php esc_attr_e( 'Before performing an import, we strongly recommend that you make a backup of your site.', 'slim-seo' ); ?></strong></p>

<div class="ef-control">
	<div class="ef-control__label">
		<label for="source"><?php esc_html_e( 'Migrate SEO data from:', 'slim-seo' ); ?></label>
	</div>
	<div class="ef-control__input">
		<div class="ss-input-wrapper" style="display: flex;">
			<select name="source" id="source">
				<optgroup value="meta" label="<?php esc_html_e( 'SEO plugins', 'slim-seo' ); ?>">
					<?php $sources = SlimSEO\Helpers\Data::get_migration_sources( 'meta' ) ?>
					<?php foreach ( $sources as $id => $source ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $source ); ?></option>
					<?php endforeach ?>
				</optgroup>

				<optgroup value="redirection" label="<?php esc_html_e( 'Redirection plugins', 'slim-seo' ); ?>">
					<?php $sources = SlimSEO\Helpers\Data::get_migration_sources( 'redirection' ) ?>
					<?php foreach ( $sources as $id => $source ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $source ); ?></option>
					<?php endforeach ?>
				</optgroup>
			</select>
			<button type="button" class="button button-primary" id="process"><?php esc_html_e( 'Migrate', 'slim-seo' ); ?></button>
		</div>
		<div class="migration-status">
			<div id="posts-status"></div>
			<div id="terms-status"></div>
			<div id="redirects-status"></div>
			<div id="robots-status"></div>
			<div id="done-status"></div>
		</div>
	</div>
</div>

<h3><?php esc_attr_e( 'ChatGPT API key', 'slim-seo' ); ?></h3>
<div class="ef-control">
	<div class="ef-control__label">
		<label for="ss-chatgpt-key"><?php esc_html_e( 'Key:', 'slim-seo' ); ?></label>
	</div>
	<div class="ef-control__input">
		<div class="ss-input-wrapper">
			<input type="text" name="slim_seo[chatgpt_key]" id="ss-chatgpt-key" autocomplete="autocomplete_off_facebook_app"  value="<?php echo esc_attr( $data['chatgpt_key'] ?? '' ); ?>">
		</div>
	</div>
</div>

<?php do_action( 'slim_seo_tools_tab_content' ); ?>

<?php submit_button( __( 'Save Changes', 'slim-seo' ) ); ?>
