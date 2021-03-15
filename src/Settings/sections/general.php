<div id="general" class="ss-tab-pane ss-is-active">
	<p><?php esc_html_e( 'Toggle the features you want to use on your website.', 'slim-seo' ); ?></p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="meta_title"<?php checked( $this->is_feature_active( 'meta_title' ) ) ?>>
			<?php esc_html_e( 'Meta title', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate meta title tag' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="meta_description"<?php checked( $this->is_feature_active( 'meta_description' ) ) ?>>
			<?php esc_html_e( 'Meta description', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate meta description tag' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="meta_robots"<?php checked( $this->is_feature_active( 'meta_robots' ) ) ?>>
			<?php esc_html_e( 'Meta robots', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate meta robots tag' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="open_graph"<?php checked( $this->is_feature_active( 'open_graph' ) ) ?>>
			<?php esc_html_e( 'Open Graph', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate Open Graph meta tags for sharing on Facebook and other social networks' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="twitter_cards"<?php checked( $this->is_feature_active( 'twitter_cards' ) ) ?>>
			<?php esc_html_e( 'Twitter cards', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate Twitter cards meta tags for sharing on Twitter' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="canonical_url"<?php checked( $this->is_feature_active( 'canonical_url' ) ) ?>>
			<?php esc_html_e( 'Canonical URL', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate canonical URL to avoid duplicated content' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="rel_links"<?php checked( $this->is_feature_active( 'rel_links' ) ) ?>>
			<?php esc_html_e( '"rel" links', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate "rel" links' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="sitemaps"<?php checked( $this->is_feature_active( 'sitemaps' ) ) ?>>
			<?php esc_html_e( 'Sitemaps', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate XML sitemap to help search engine crawl and index the website content' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="images_alt"<?php checked( $this->is_feature_active( 'images_alt' ) ) ?>>
			<?php esc_html_e( 'Image alt text', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate "alt" text for images when inserting into post content' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="breadcrumbs"<?php checked( $this->is_feature_active( 'breadcrumbs' ) ) ?>>
			<?php esc_html_e( 'Breadcrumbs', 'falcon' ) ?>
			<?= $this->tooltip( 'Enable breadcrumbs shortcode for inserting into your template' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="auto_redirection"<?php checked( $this->is_feature_active( 'auto_redirection' ) ) ?>>
			<?php esc_html_e( 'Auto redirection', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically redirect pages if needed' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="feed"<?php checked( $this->is_feature_active( 'feed' ) ) ?>>
			<?php esc_html_e( 'RSS feed', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically add a back link to posts to prevent content from being copied' ) ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="slim_seo[features][]" value="schema"<?php checked( $this->is_feature_active( 'schema' ) ) ?>>
			<?php esc_html_e( 'Schema', 'falcon' ) ?>
			<?= $this->tooltip( 'Automatically generate unified schema graph to help search engines understand the website structure' ) ?>
		</label>
	</p>
	<?php submit_button( __( 'Save Changes', 'slim-seo' ) ); ?>
</div>