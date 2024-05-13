<?php
use SlimSEO\Helpers\UI;

// phpcs:ignore
$features = [
	// Translators: %s: link to the docs.
	'meta_title'       => [ __( 'Meta title', 'slim-seo' ), sprintf( __( 'Automatically generate <a href="%s" target="_blank">meta title tag</a>.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/meta-title-tag/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	// Translators: %s: link to the docs.
	'meta_description' => [ __( 'Meta description', 'slim-seo' ), sprintf( __( 'Automatically generate <a href="%s" target="_blank">meta description tag</a> based on the post excerpt or content.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/meta-description-tag/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	// Translators: %s: link to the docs.
	'meta_robots'      => [ __( 'Meta robots', 'slim-seo' ), sprintf( __( 'Automatically generate <a href="%s" target="_blank">meta robots tag</a>.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/meta-robots-tag/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	// Translators: %s: link to the docs.
	'open_graph'       => [ __( 'Open Graph', 'slim-seo' ), sprintf( __( 'Automatically generate <a href="%s" target="_blank">Open Graph tags</a> for sharing on Facebook and other social networks.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/facebook-open-graph-tags/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	// Translators: %s: link to the docs.
	'twitter_cards'    => [ __( 'Twitter cards', 'slim-seo' ), sprintf( __( 'Automatically generate <a href="%s" target="_blank">Twitter cards tags</a> for sharing on Twitter.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/twitter-card-tags/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	'canonical_url'    => [ __( 'Canonical URL', 'slim-seo' ), __( 'Automatically generate canonical URL to avoid duplicated content.', 'slim-seo' ) ],
	'rel_links'        => [ __( '"rel" links', 'slim-seo' ), __( 'Automatically generate "rel" links for previous and next pages.', 'slim-seo' ) ],
	// Translators: %1$s: link to the docs, %2$s: sitemap URL.
	'sitemaps'         => [ __( 'Sitemaps', 'slim-seo' ), sprintf( __( 'Automatically generate <a href="%1$s" target="_blank">XML sitemap</a> to help search engine crawl and index the website content. <a href="%2$s" target="_blank">View your sitemap</a>.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/xml-sitemap/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo', home_url( 'sitemap.xml' ) ) ],
	// Translators: %s: link to the docs.
	'images_alt'       => [ __( 'Image alt text', 'slim-seo' ), sprintf( __( 'Automatically <a href="%s" target="_blank">generate "alt" text for images</a> when inserting into post content.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/image-alt-text/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	// Translators: %s: link to the docs.
	'breadcrumbs'      => [ __( 'Breadcrumbs', 'slim-seo' ), sprintf( __( 'Enable the <a href="%s" target="_blank">breadcrumbs shortcode</a> for inserting into your template.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/breadcrumbs/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	// Translators: %s: link to the docs.
	'feed'             => [ __( 'RSS feed', 'slim-seo' ), sprintf( __( 'Automatically <a href="%s" target="_blank">add a back link</a> to posts to prevent content from being copied.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/rss-feed/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	// Translators: %s: link to the docs.
	'schema'           => [ __( 'Schema', 'slim-seo' ), sprintf( __( 'Automatically <a href="%s" target="_blank">generate unified schema graph</a> to help search engines understand the website structure.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/schema/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
	// Translators: %s: link to the docs.
	'redirection'      => [ __( 'Redirection', 'slim-seo' ), sprintf( __( '<a href="%s" target="_blank">Create redirects</a> for broken pages, unimportant pages, or old URLs to existing working URLs.', 'slim-seo' ), 'https://docs.wpslimseo.com/slim-seo/redirection/?utm_source=settings_page&utm_medium=link&utm_campaign=slim_seo' ) ],
];

echo '<p>', esc_html__( 'Toggle the features you want to use on your website.', 'slim-seo' ), '</p>';

// phpcs:ignore
foreach ( $features as $key => $text ) {
	UI::feature_box( 'slim_seo[features][]', $key, $this->is_feature_active( $key ), $text[0], $text[1] );
}

submit_button( __( 'Save Changes', 'slim-seo' ) );
