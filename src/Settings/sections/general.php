<div id="general" class="ss-tab-pane ss-is-active">
	<h3><?php esc_attr_e( 'Features', 'slim-seo' ); ?></h3>
	<p><?php esc_html_e( 'Toggle the features you want to use on your website.', 'slim-seo' ); ?></p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="meta_title"<?php checked( $this->is_feature_active( 'meta_title' ) ) ?>>
			<?php esc_html_e( 'Meta title', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="meta_description"<?php checked( $this->is_feature_active( 'meta_description' ) ) ?>>
			<?php esc_html_e( 'Meta description', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="meta_robots"<?php checked( $this->is_feature_active( 'meta_robots' ) ) ?>>
			<?php esc_html_e( 'Meta robots', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="open_graph"<?php checked( $this->is_feature_active( 'open_graph' ) ) ?>>
			<?php esc_html_e( 'Facebook open graph', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="twitter_cards"<?php checked( $this->is_feature_active( 'twitter_cards' ) ) ?>>
			<?php esc_html_e( 'Twitter cards', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="canonical_url"<?php checked( $this->is_feature_active( 'canonical_url' ) ) ?>>
			<?php esc_html_e( 'Canonical URL', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="rel_links"<?php checked( $this->is_feature_active( 'rel_links' ) ) ?>>
			<?php esc_html_e( '"rel" links', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="sitemaps"<?php checked( $this->is_feature_active( 'sitemaps' ) ) ?>>
			<?php esc_html_e( 'Sitemaps', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="images_alt"<?php checked( $this->is_feature_active( 'images_alt' ) ) ?>>
			<?php esc_html_e( 'Image alt text', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="breadcrumbs"<?php checked( $this->is_feature_active( 'breadcrumbs' ) ) ?>>
			<?php esc_html_e( 'Breadcrumbs', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="auto_redirection"<?php checked( $this->is_feature_active( 'auto_redirection' ) ) ?>>
			<?php esc_html_e( 'Auto redirection', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="feed"<?php checked( $this->is_feature_active( 'feed' ) ) ?>>
			<?php esc_html_e( 'RSS feed', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="cleaner"<?php checked( $this->is_feature_active( 'cleaner' ) ) ?>>
			<?php esc_html_e( 'Header cleaner', 'falcon' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="schema"<?php checked( $this->is_feature_active( 'schema' ) ) ?>>
			<?php esc_html_e( 'Schema', 'falcon' ) ?>
		</label>
	</p>
	<?php submit_button( __( 'Save', 'slim-seo' ) ); ?>
</div>