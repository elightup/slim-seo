<div class="ss-field">
	<div class="ss-label">
		<label for="ss-title"><?php esc_html_e( 'Meta title', 'slim-seo' ); ?></label>
	</div>
	<div class="ss-input">
		<input type="text" id="ss-title" name="slim_seo[home_title]" value="<?= esc_attr( $data['home_title'] ); ?>">
		<p class="description">
			<?php esc_html_e( 'Character count:', 'slim-seo' ); ?>
			<span class="ss-counter">0</span>.
			<?php esc_html_e( 'Recommended length: ≤ 60 characters.', 'slim-seo' ); ?>
		</p>
	</div>
</div>
<div class="ss-field">
	<div class="ss-label">
		<label for="ss-description"><?php esc_html_e( 'Meta description', 'slim-seo' ); ?></label>
	</div>
	<div class="ss-input">
		<textarea id="ss-description" name="slim_seo[home_description]" rows="3"><?= esc_textarea( $data['home_description'] ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Character count:', 'slim-seo' ); ?>
			<span class="ss-counter">0</span>.
			<?php esc_html_e( 'Recommended length: 50-160 characters.', 'slim-seo' ); ?>
		</p>
	</div>
</div>
<div class="ss-field">
	<div class="ss-label">
		<label for="ss-facebook-image"><?php esc_html_e( 'Facebook image', 'slim-seo' ); ?></label>
	</div>
	<div class="ss-input">
		<div class="ss-input-group">
			<input type="text" id="ss-facebook-image" name="slim_seo[home_facebook_image]" value="<?= esc_attr( $data['home_facebook_image'] ); ?>">
			<button class="ss-select-image button"><?php esc_html_e( 'Select image', 'slim-seo' ); ?></button>
		</div>
		<p class="description">
			<?php esc_html_e( 'Recommended size: 1200x630 px.', 'slim-seo' ); ?>
		</p>
	</div>
</div>
<div class="ss-field">
	<div class="ss-label">
		<label for="ss-twitter-image"><?php esc_html_e( 'Twitter image', 'slim-seo' ); ?></label>
	</div>
	<div class="ss-input">
		<div class="ss-input-group">
			<input type="text" id="ss-twitter-image" name="slim_seo[home_twitter_image]" value="<?= esc_attr( $data['home_twitter_image'] ); ?>">
			<button class="ss-select-image button"><?php esc_html_e( 'Select image', 'slim-seo' ); ?></button>
		</div>
		<p class="description">
			<?php esc_html_e( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ); ?>
		</p>
	</div>
</div>

<?php submit_button( __( 'Save Changes', 'slim-seo' ) ); ?>
