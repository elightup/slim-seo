<h3><?php esc_attr_e( 'Default Social Images', 'slim-seo' ); ?></h3>
<p><?php esc_html_e( 'These images are used when the post/page being shared does not have featured image or social images set up.', 'slim-seo' ); ?></p>

<div class="ss-field">
	<div class="ss-label">
		<label for="ss-default-facebook-image"><?php esc_html_e( 'Facebook image', 'slim-seo' ); ?></label>
	</div>
	<div class="ss-input">
		<div class="ss-input-group">
			<input type="text" id="ss-default-facebook-image" name="slim_seo[default_facebook_image]" value="<?= esc_attr( $data['default_facebook_image'] ); ?>">
			<button class="ss-select-image button"><?php esc_html_e( 'Select image', 'slim-seo' ); ?></button>
		</div>
		<p class="description">
			<?php esc_html_e( 'Recommended size: 1200x630 px.', 'slim-seo' ); ?>
		</p>
	</div>
</div>

<div class="ss-field">
	<div class="ss-label">
		<label for="ss-default-twitter-image"><?php esc_html_e( 'Twitter image', 'slim-seo' ); ?></label>
	</div>
	<div class="ss-input">
		<div class="ss-input-group">
			<input type="text" id="ss-default-twitter-image" name="slim_seo[default_twitter_image]" value="<?= esc_attr( $data['default_twitter_image'] ); ?>">
			<button class="ss-select-image button"><?php esc_html_e( 'Select image', 'slim-seo' ); ?></button>
		</div>
		<p class="description">
			<?php esc_html_e( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ); ?>
		</p>
	</div>
</div>

<h3><?php esc_attr_e( 'Social Media Analytics', 'slim-seo' ); ?></h3>
<p><?php esc_html_e( 'If you are using Facebook or Twitter analytic tools, enter the details below. Omitting them has no effect on how a shared web page appears on a Facebook timeline or Twitter feed.', 'slim-seo' ); ?></p>

<div class="ss-field">
	<div class="ss-label">
		<label for="ss-facebook-app-id"><?php esc_html_e( 'Facebook app ID', 'slim-seo' ); ?></label>
	</div>
	<div class="ss-input">
		<div class="ss-input-group">
			<input type="text" id="ss-facebook-app-id" name="slim_seo[facebook_app_id]" value="<?= esc_attr( $data['facebook_app_id'] ); ?>">
		</div>
	</div>
</div>

<div class="ss-field">
	<div class="ss-label">
		<label for="ss-twitter-site"><?php esc_html_e( 'Twitter site', 'slim-seo' ); ?></label>
	</div>
	<div class="ss-input">
		<div class="ss-input-group">
			<input type="text" id="ss-twitter-site" name="slim_seo[twitter_site]" value="<?= esc_attr( $data['twitter_site'] ); ?>" placeholder="@account_name">
		</div>
	</div>
</div>

<?php submit_button( __( 'Save Changes', 'slim-seo' ) ); ?>
