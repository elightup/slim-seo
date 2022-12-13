<p><?php esc_html_e( 'Toggle the features you want to use on your website.', 'slim-seo' ); ?></p>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="meta_title"<?php checked( $this->is_feature_active( 'meta_title' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Meta title', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate meta title tag', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="meta_description"<?php checked( $this->is_feature_active( 'meta_description' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Meta description', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate meta description tag', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="meta_robots"<?php checked( $this->is_feature_active( 'meta_robots' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Meta robots', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate meta robots tag', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="open_graph"<?php checked( $this->is_feature_active( 'open_graph' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Open Graph', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate Open Graph meta tags for sharing on Facebook and other social networks', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="twitter_cards"<?php checked( $this->is_feature_active( 'twitter_cards' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Twitter cards', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate Twitter cards meta tags for sharing on Twitter', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="canonical_url"<?php checked( $this->is_feature_active( 'canonical_url' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Canonical URL', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate canonical URL to avoid duplicated content', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="rel_links"<?php checked( $this->is_feature_active( 'rel_links' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( '"rel" links', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate "rel" links', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="sitemaps"<?php checked( $this->is_feature_active( 'sitemaps' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Sitemaps', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate XML sitemap to help search engine crawl and index the website content', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="images_alt"<?php checked( $this->is_feature_active( 'images_alt' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Image alt text', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate "alt" text for images when inserting into post content', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="breadcrumbs"<?php checked( $this->is_feature_active( 'breadcrumbs' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Breadcrumbs', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Enable breadcrumbs shortcode for inserting into your template', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="feed"<?php checked( $this->is_feature_active( 'feed' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'RSS feed', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically add a back link to posts to prevent content from being copied', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="schema"<?php checked( $this->is_feature_active( 'schema' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Schema', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Automatically generate unified schema graph to help search engines understand the website structure', 'slim-seo' ) ) ?>
</div>
<div class="ss-feature">
	<span class="ss-toggle">
		<input type="checkbox" name="slim_seo[features][]" value="redirection"<?php checked( $this->is_feature_active( 'redirection' ) ); ?>>
		<div class="ss-toggle__switch"></div>
		<span class="ss-toggle__label"><?php esc_html_e( 'Redirection', 'slim-seo' ); ?></span>
	</span>
	<?php $this->tooltip( __( 'Redirect broken pages, unimportant pages, or old URLs to existing working URLs', 'slim-seo' ) ) ?>
</div>
<?php submit_button( __( 'Save Changes', 'slim-seo' ) ); ?>
