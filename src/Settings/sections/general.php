<?php
use SlimSEO\Helpers\UI;

// List of features: key => [title, tooltip].
$features = [
	'meta_title'       => [ __( 'Meta title', 'slim-seo' ), __( 'Automatically generate meta title tag', 'slim-seo' ) ],
	'meta_description' => [ __( 'Meta description', 'slim-seo' ), __( 'Automatically generate meta description tag', 'slim-seo' ) ],
	'meta_robots'      => [ __( 'Meta robots', 'slim-seo' ), __( 'Automatically generate meta robots tag', 'slim-seo' ) ],
	'open_graph'       => [ __( 'Open Graph', 'slim-seo' ), __( 'Automatically generate Open Graph meta tags for sharing on Facebook and other social networks', 'slim-seo' ) ],
	'twitter_cards'    => [ __( 'Twitter cards', 'slim-seo' ), __( 'Automatically generate Twitter cards meta tags for sharing on Twitter', 'slim-seo' ) ],
	'canonical_url'    => [ __( 'Canonical URL', 'slim-seo' ), __( 'Automatically generate canonical URL to avoid duplicated content', 'slim-seo' ) ],
	'rel_links'        => [ __( '"rel" links', 'slim-seo' ), __( 'Automatically generate "rel" links', 'slim-seo' ) ],
	'sitemaps'         => [ __( 'Sitemaps', 'slim-seo' ), __( 'Automatically generate XML sitemap to help search engine crawl and index the website content', 'slim-seo' ) ],
	'images_alt'       => [ __( 'Image alt text', 'slim-seo' ), __( 'Automatically generate "alt" text for images when inserting into post content', 'slim-seo' ) ],
	'breadcrumbs'      => [ __( 'Breadcrumbs', 'slim-seo' ), __( 'Enable breadcrumbs shortcode for inserting into your template', 'slim-seo' ) ],
	'feed'             => [ __( 'RSS feed', 'slim-seo' ), __( 'Automatically add a back link to posts to prevent content from being copied', 'slim-seo' ) ],
	'schema'           => [ __( 'Schema', 'slim-seo' ), __( 'Automatically generate unified schema graph to help search engines understand the website structure', 'slim-seo' ) ],
	'redirection'      => [ __( 'Redirection', 'slim-seo' ), __( 'Redirect broken pages, unimportant pages, or old URLs to existing working URLs', 'slim-seo' ) ],
];

echo '<p>', esc_html__( 'Toggle the features you want to use on your website.', 'slim-seo' ), '</p>';

foreach ( $features as $key => $text ) {
	echo '<div class="ss-field">';
	UI::toggle( 'slim_seo[features][]', $key, $this->is_feature_active( $key ), $text[0] );
	UI::tooltip( $text[1] );
	echo '</div>';
}

submit_button( __( 'Save Changes', 'slim-seo' ) );
