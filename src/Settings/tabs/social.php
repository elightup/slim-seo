<?php defined( 'ABSPATH' ) || die ?>

<?php if ( $this->is_feature_active( 'open_graph' ) || $this->is_feature_active( 'twitter_cards' ) ) : ?>
	<h3><?php esc_attr_e( 'Default Social Images', 'slim-seo' ); ?></h3>
	<p><?php esc_html_e( 'These images are used when the post/page being shared does not have featured image or social images set up.', 'slim-seo' ); ?></p>

	<?php if ( $this->is_feature_active( 'open_graph' ) ) : ?>
		<div class="ef-control">
			<div class="ef-control__label">
				<label for="ss-default-facebook-image"><?php esc_html_e( 'Facebook image', 'slim-seo' ); ?></label>
			</div>
			<div class="ef-control__input">
				<div class="ss-input-wrapper">
					<input type="text" autocomplete="autocomplete_off_facebook" id="ss-default-facebook-image" name="slim_seo[default_facebook_image]" value="<?php echo esc_attr( $data['default_facebook_image'] ); ?>">
					<button type="button" class="components-button ss-select-image has-icon">
						<span class="dashicon dashicons dashicons-format-image"></span>
					</button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Recommended size: 1200x630 px. Should have 1.91:1 aspect ratio with width ≥ 600 px.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $this->is_feature_active( 'twitter_cards' ) ) : ?>
		<div class="ef-control">
			<div class="ef-control__label">
				<label for="ss-default-twitter-image"><?php esc_html_e( 'Twitter image', 'slim-seo' ); ?></label>
			</div>
			<div class="ef-control__input">
				<div class="ss-input-wrapper">
					<input type="text" autocomplete="autocomplete_off_twitter" id="ss-default-twitter-image" name="slim_seo[default_twitter_image]" value="<?php echo esc_attr( $data['default_twitter_image'] ); ?>">
					<button type="button" class="components-button ss-select-image has-icon">
						<span class="dashicon dashicons dashicons-format-image"></span>
					</button>
				</div>
				<p class="description">
					<?php esc_html_e( 'Recommended size: 1200x600 px. Should have 2:1 aspect ratio with width ≥ 300 px and ≤ 4096 px.', 'slim-seo' ); ?>
				</p>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>

<h3><?php esc_attr_e( 'Social Media Analytics', 'slim-seo' ); ?></h3>
<p><?php esc_html_e( 'If you are using Facebook or Twitter analytic tools, enter the details below. Omitting them has no effect on how a shared web page appears on a Facebook timeline or Twitter feed.', 'slim-seo' ); ?></p>

<div class="ef-control">
	<div class="ef-control__label">
		<label for="ss-facebook-app-id"><?php esc_html_e( 'Facebook app ID', 'slim-seo' ); ?></label>
	</div>
	<div class="ef-control__input">
		<div class="ss-input-group">
			<input type="text" autocomplete="autocomplete_off_facebook_app" id="ss-facebook-app-id" name="slim_seo[facebook_app_id]" value="<?php echo esc_attr( $data['facebook_app_id'] ); ?>">
		</div>
	</div>
</div>

<div class="ef-control">
	<div class="ef-control__label">
		<label for="ss-twitter-site"><?php esc_html_e( 'Twitter site', 'slim-seo' ); ?></label>
	</div>
	<div class="ef-control__input">
		<div class="ss-input-wrapper">
			<input type="text" autocomplete="autocomplete_off_twitter_site" id="ss-twitter-site" name="slim_seo[twitter_site]" value="<?php echo esc_attr( $data['twitter_site'] ); ?>" placeholder="@account_name">
		</div>
	</div>
</div>

<?php submit_button( __( 'Save Changes', 'slim-seo' ) ); ?>
