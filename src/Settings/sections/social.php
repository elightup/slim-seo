<div id="social" class="ss-tab-pane">
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
				<?php esc_html_e( 'Recommended size: 1200x628 px.', 'slim-seo' ); ?>
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
				<?php esc_html_e( 'Recommended size: 800x418 px.', 'slim-seo' ); ?>
			</p>
		</div>
	</div>

	<?php submit_button( __( 'Save', 'slim-seo' ) ); ?>

</div>
