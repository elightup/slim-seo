<p><?php esc_html_e( 'Use these settings to insert code from Google Tag Manager, Google Analytics or webmaster tools verification.', 'slim-seo' ); ?></p>
<table class="form-table">
	<tr>
		<th scope="row">
			<label for="header-code"><?php esc_html_e( 'Header Code', 'slim-seo' ); ?></label>
		</th>
		<td>
			<textarea id="header-code" class="large-text code" rows="10" name="slim_seo[header_code]"><?= esc_attr( $data['header_code'] ); ?></textarea>
			<p class="description"><?= wp_kses_post( __( 'Code entered in this box will be printed in the <code>&lt;head&gt;</code> section.', 'slim-seo' ) ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="body-code"><?php esc_html_e( 'Body Code', 'slim-seo' ); ?></label>
		</th>
		<td>
			<textarea id="body-code" class="large-text code" rows="10" name="slim_seo[body_code]"><?= esc_attr( $data['body_code'] ); ?></textarea>
			<p class="description"><?= wp_kses_post( __( 'Code entered in this box will be printed after the opening <code>&lt;body&gt;</code> tag.', 'slim-seo' ) ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="footer-code"><?php esc_html_e( 'Footer Code', 'slim-seo' ); ?></label>
		</th>
		<td>
			<textarea id="footer-code" class="large-text code" rows="10" name="slim_seo[footer_code]"><?= esc_attr( $data['footer_code'] ); ?></textarea>
			<p class="description"><?= wp_kses_post( __( 'Code entered in this box will be printed before the closing <code>&lt;/body&gt;</code> tag.', 'slim-seo' ) ); ?></p>
		</td>
	</tr>
</table>

<?php submit_button( __( 'Save Changes', 'slim-seo' ) ); ?>
