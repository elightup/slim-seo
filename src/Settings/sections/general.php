<p><?php esc_html_e( 'Toggle the features you want to use on your website.', 'slim-seo' ); ?></p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="meta_title"<?php checked( $this->is_feature_active( 'meta_title' ) ); ?>>
		<?php esc_html_e( 'Meta title', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate meta title tag', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="meta_description"<?php checked( $this->is_feature_active( 'meta_description' ) ); ?>>
		<?php esc_html_e( 'Meta description', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate meta description tag', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="meta_robots"<?php checked( $this->is_feature_active( 'meta_robots' ) ); ?>>
		<?php esc_html_e( 'Meta robots', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate meta robots tag', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="open_graph"<?php checked( $this->is_feature_active( 'open_graph' ) ); ?>>
		<?php esc_html_e( 'Open Graph', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate Open Graph meta tags for sharing on Facebook and other social networks', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="twitter_cards"<?php checked( $this->is_feature_active( 'twitter_cards' ) ); ?>>
		<?php esc_html_e( 'Twitter cards', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate Twitter cards meta tags for sharing on Twitter', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="canonical_url"<?php checked( $this->is_feature_active( 'canonical_url' ) ); ?>>
		<?php esc_html_e( 'Canonical URL', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate canonical URL to avoid duplicated content', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="rel_links"<?php checked( $this->is_feature_active( 'rel_links' ) ); ?>>
		<?php esc_html_e( '"rel" links', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate "rel" links', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="sitemaps"<?php checked( $this->is_feature_active( 'sitemaps' ) ); ?>>
		<?php esc_html_e( 'Sitemaps', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate XML sitemap to help search engine crawl and index the website content', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="images_alt"<?php checked( $this->is_feature_active( 'images_alt' ) ); ?>>
		<?php esc_html_e( 'Image alt text', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate "alt" text for images when inserting into post content', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="breadcrumbs"<?php checked( $this->is_feature_active( 'breadcrumbs' ) ); ?>>
		<?php esc_html_e( 'Breadcrumbs', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Enable breadcrumbs shortcode for inserting into your template', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="auto_redirection"<?php checked( $this->is_feature_active( 'auto_redirection' ) ); ?>>
		<?php esc_html_e( 'Auto redirection', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically redirect pages if needed', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="feed"<?php checked( $this->is_feature_active( 'feed' ) ); ?>>
		<?php esc_html_e( 'RSS feed', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically add a back link to posts to prevent content from being copied', 'slim-seo' ) ) ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="slim_seo[features][]" value="schema"<?php checked( $this->is_feature_active( 'schema' ) ); ?>>
		<?php esc_html_e( 'Schema', 'slim-seo' ); ?>
		<?php $this->tooltip( __( 'Automatically generate unified schema graph to help search engines understand the website structure', 'slim-seo' ) ) ?>
	</label>
</p>
<?php submit_button( __( 'Save Changes', 'slim-seo' ) ); ?>
