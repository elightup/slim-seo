=== Slim SEO - Fast & Automated WordPress SEO Plugin ===
Contributors: elightup, rilwis, hungviet91, barcavn2
Donate link: https://wpslimseo.com/products/
Tags: seo, search engine optimization, schema, sitemap, google, facebook, twitter, meta tags, meta description, open graph, twitter card, xml sitemap
Requires at least: 5.7
Tested up to: 6.2.1
Requires PHP: 7.1
Stable tag: 3.20.2
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A full-featured SEO plugin for WordPress that's lightweight, blazing fast with minimum configuration. No bloats and just works!

== Description ==

### A Fast & Automated SEO Plugin For WordPress

Currently there are many SEO plugins for WordPress in the market. But these plugins often have too many options and are very complicated for ordinary users. Access to their configuration section, you will easily get lost in a maze of explanations and options that you sometimes don't understand. Besides, there are ads!

**So how can an ordinary user use an SEO plugin?**

SEO should be an integrated part of WordPress, where users don't need or need very little effort to configure for SEO. The main reason is that not everyone understands the terms of SEO and how to configure them optimally.

So, we made [**Slim SEO**](https://wpslimseo.com).

Slim SEO is a full-featured SEO plugin, that's done right! It provides a complete SEO solution for WordPress where the configuration has been done automatically. Users do not need to care about their complex and semantic options.

So what does Slim SEO do?

### Slim SEO Features

Slim SEO helps you do the following jobs automatically:

#### 1. Meta Tags

The following meta tags are auto-generated and optimized for the best SEO scores.

- [Meta title tag](https://docs.wpslimseo.com/slim-seo/meta-title-tag/): display your title in a SEO-friendly way.
- [Meta description tag](https://docs.wpslimseo.com/slim-seo/meta-description-tag/): auto generate from posts/pages excerpt or content.
- [Meta robots tag](https://docs.wpslimseo.com/slim-seo/meta-robots-tag/): decide which pages are indexed and which ones not.
- [Facebook Open Graph Tags](https://docs.wpslimseo.com/slim-seo/facebook-open-graph-tags/): share your posts on Facebook beautifully.
- [Twitter Card Tags](https://docs.wpslimseo.com/slim-seo/twitter-card-tags/): share your posts on Twitter beautifully.

#### 2. [XML Sitemap](https://docs.wpslimseo.com/slim-seo/xml-sitemap/)

Slim SEO automatically generates XML sitemap (at `domain.com/sitemap.xml`) to submit to search engines. With XML sitemaps, your website are indexed fast and completely.

#### 3. [Breadcrumbs](https://docs.wpslimseo.com/slim-seo/breadcrumbs/)

The plugin allows you to output a breadcrumb trail on your website easily. It automatically fetches the information from the current post and output a hierarchy for you. You can also style the breadcrumbs to match your theme style.

#### 4. [Schema (Structured Data)](https://docs.wpslimseo.com/slim-seo/schema/)

Schema is a way that describes structured data for search engines. Based on the data provided, search engines can show the content in the search results page in a more appealing way.

Slim SEO automatically adds the some structured data to the website via JSON-LD which makes your website more SEO-friendly. Not only schemas are created by the plugin, there are also meaningful connections between them. For example, an article (single post) is the main entity of the current webpage. Slim SEO does that all without any configuration.

#### 5. [Redirection](https://docs.wpslimseo.com/slim-seo/redirection/)

- Setting up redirection rules easily
- Auto redirect non-www to www and vice versa
- 404 link monitoring

#### 6. And many more

- [Inserting Google Analytics, Facebook pixel or any code to the header or footer](https://docs.wpslimseo.com/slim-seo/header-footer-code/) of the site
- Auto prevent scraping content from [RSS feed](https://docs.wpslimseo.com/slim-seo/rss-feed/)
- [Integrations](https://docs.wpslimseo.com/slim-seo/integrations/) with many plugins, including page builders
- [Import and export](https://docs.wpslimseo.com/slim-seo/import-export/) data or migrate data from popular SEO plugins
- Auto redirect if post slug changed

### Premium plugins

- [Slim SEO Schema](https://wpslimseo.com/slim-seo-schema/): an advanced and comprehensive schema builder for your WordPress websites, which allow you to add schema to your custom post types via a visual and user-friendly UI.
- [Slim SEO Link Manager](https://wpslimseo.com/slim-seo-link-manager/): build and monitor internal links in your WordPress websites in real-time with advanced & insightful reports.

[View all](https://wpslimseo.com/products/)

### Who should use Slim SEO?

Everyone can use Slim SEO!

However, Slim SEO is perfectly suitable for users who prefer simplicity or do not like the complicated options that other SEO plugins provide. It's also a good choice for users with little SEO knowledge and just want to use SEO plugins to automate their jobs.

## You might also like

If you love this plugin, please try our other great products:

- [Meta Box](https://metabox.io): The best plugin for adding custom meta boxes and custom fields for WordPress
- [GretaThemes](https://gretathemes.com): Beautiful and easy to use WordPress themes
- [Auto Listings](https://wpautolistings.com): The best WordPress plugin for car dealership & car listings websites.

== Installation ==

Before installing, please note that the plugin requires PHP >= 5.6.

1. Go to Plugins > Add New.
2. Search for "Slim SEO".
3. Install and activate the plugin.

That's all. The plugin doesn't have any settings page. Everything is done automatically.

== Frequently Asked Questions ==

= How meta title tag is generated? =

WordPress already has this featured! All we need to do is add theme support for `title-tag`. By default, the title tag will have the following format:

For homepage: Site title - Site description
For single page or post: Page/Post title - Site title
For other pages: Page title - Site title

This format is pretty good!

= How meta description tag is generated? =

The meta description tag is automatically generated from the post excerpt or post content (in case you didn't enter post excerpt). For categories, post tags or custom taxonomies, their description will be used as meta description.

= How Open Graph meta tags is generated? =

Open Graph inherits the meta title and meta description. For image tag, it gets from the featured image.

For single posts, the plugin also provides detailed information for article section (the first post category) and article tags (post tags). It also provides additional information such as published time and modified time.

Other tags such as `url`, `site_name`, `type`, `locale` are quite obvious.

= How Twitter card meta tags is generated? =

Twitter inherits some tags from Open Graph such as title, description and image. So we only need to set the card type to large image, so the posts appear beautifully on Twitter.

= How do I set meta tags for my homepage? =

If your homepage is a static page, then it's treated like a normal page. The meta description is generated from page content, and the featured image will be used for Open Graph tags.

If your homepage shows latest posts, then it's already done automatically by the plugin.

= Can I change the meta tags manually? =

Yes, you can. When you edit a post or a page, there's a "Search Engine Optimization" meta box below the editor, which allows you to edit meta tags if needed.

= Where is my XML sitemap? =

The sitemap URL is located at `domain.com/sitemap.xml`.

== Screenshots ==

== Upgrade Notice ==

== Changelog ==

= 3.20.2 - 2023-05-19 =
- Add support for WPForms and MailPoet
- Fix wrong text domain.
- Fix bug when Admin Columns plugin removes the meta title column

= 3.20.1 - 2023-04-25 =

Based on the user feedback, we decide to disable user sitemap by default. You can still enable it if needed with the snippet:

`add_filter( 'slim_seo_user_sitemap', '__return_true' );`

You might want to re-save the permalinks to update the rewrite rules.

Other fixes:
- Improve the Bricks integration to work with pages that use templates

= 3.20.0 - 2023-04-20 =
- Add user sitemap
- Add breadcrumbs block, useful to be used with the Full Site Editor
- Add Pinterest rich pins for WooCommerce
- Improve Bricks + WPGB integration, ignore elements with query loop
- Exclude Bricks templates from the sitemap
- Skip rendering some blocks for meta description & Open Graph, which cause custom CSS issue with FluentForms
- Fix migrating from AIO SEO plugin, due to its recent changed
- Fix redirect not working with mailto: link
- Fix compatibility with Auto Listings & LifterLMS

= 3.19.1 - 2023-04-14 =
- Exclude external images from the sitemap
- Fix Bricks dynamic tags not rendering
- Fix clicking on canonical field focusing on title field

= 3.19.0 - 2023-04-04 =
- Add canonical URL
- If a page is set as the post type archive, get the page title for the breadcrumbs
- Make breadcrumbs include product categories for WooCommerce
- Fix rel link for the 1st page containing /page/1

= 3.18.2 - 2023-03-21 =
- Add type safe for checking terms in breadcrumbs
- Fix regex redirection causing PHP warnings
- Fix link attributes in WP 6.0

= 3.18.1 - 2023-02-22 =

- Make plugin name clear for translators (#76). Credit Alex Lion (@alexclassroom).
- Fix PHP warning for the Google Web Stories integration
- Fix wrong reference to plugin's service list
- Fix breadcrumbs on pages with other queries

= 3.18.0 - 2023-02-17 =

- Increase min required PHP version to 7.1
- Add [rel nofollow, sponsored and ugc to links](https://wpslimseo.com/slim-seo-3-18/). Works for both classic and block editors.
- Add integration for Ultimate Member
- Improve integration for WooCommerce
- If a page is set as the post type archive, get SEO settings from that page
- Truncate the admin columns for meta and use tooltip to show it full and improve CSS for it
- Fix redirect 404 not working if 404 logs is not enabled

= 3.17.0 - 2023-02-06 =
- Add integration with AffiliateWP
- Improve compatible with Bricks & WP Grid Builder
- Redirect to the settings page after activation

= 3.16.6 - 2023-01-31 =
- Make filters to meta tags (title, description, robots) work on the admin post/term list table
- Don't add schedule or remove 404 log if not enabled
- Only run schedule for delete logs automatically if 404 log table exists
- Fix taxonomy quick edit not working

= 3.16.5 - 2023-01-03 =
- Set default OpenGraph tag og:image:alt to image title if alt text is missing
- Remove the optional OpenGraph tag article:author for privacy and security reasons
- Make default title value preview reflect the real meta title even after filtering by PHP
- Fix site title & site description has escaped characters in the input placeholder
- Fix wrong upgrade link

= 3.16.4 - 2022-12-28 =
- Set meta column widths to prevent layout broken
- Add filter for text in feed
- Deny request to post type/taxonomy sitemaps if they're filtered
- Fix redirect with regex
- Fix wrong 404 link in the report table
- Fix cannot enter spaces in redirect notes

= 3.16.3 - 2022-12-19 =
- Auto redirect if post slug changed
- Fix param type in meta title

= 3.16.2 - 2022-12-13 =
- Add integration for Auto Listings plugin
- WooCommerce integration: Fix title for shop page. Now it takes from the SEO settings and fallback to the page title.
- Updated recommended image size for Facebook and Twitter
- Hide Code tab if disable "code" feature
- Add Redirection to features list to enable/disable it if needed

= 3.16.1 - 2022-11-29 =
- Add migration redirects from other plugins
- Improve compatibility with Genesis
- Move auto redirection feature to Settings in Redirection tab
- Fix translation for JS
- Fix AIO SEO error when migration if the plugin is not activated
- Fix admin columns not available for post types created late

= 3.16.0 - 2022-11-10 =

- Add [redirection and 404 link monitoring](https://wpslimseo.com/slim-seo-3-16/).
- Improve compatibility for Divi.

= 3.15.2 - 2022-09-22 =
- Use post excerpt if available for meta description before the builder content in case of using page builders
- Fix JS warning in the settings page when migration tool is not available

= 3.15.1 - 2022-08-18 =
- Fix enqueuing wrong quick/bulk edit CSS/JS for the settings page
- Improve check for getting SEO data

= 3.15.0 - 2022-08-12 =
- Edit SEO data in quick edit and bulk edit
- Hide Tools tab if no other SEO plugins active (for migration)
- Add hooks for Twitter Cards
- Fix CSS when Elementor is active

= 3.14.2 - 2022-07-04 =
- Removed image caption and title from the sitemap as they're deprecated by Google
- Fix compatibility with the form widget (FluentForms) in Oxygen
- Fix compatibility with FluentForms

= 3.14.1 - 2022-06-21 =
- Fix parsing content from ZionBuilder brakes image slider
- Fix breadcrumbs schema output when it's inactive

= 3.14.0 - 2022-06-18 =
- Add support for Divi page builder
- Add support for Zion page builder
- Fix PHP notice when disabling breadcrumbs

= 3.13.4 - 2022-04-11 =
- Fix not showing taxonomy terms for hierarchical post types

= 3.13.3 - 2022-03-21 =
- Sitemap: don't parse shortcodes in post content
- Fix error migrating from AIO SEO

= 3.13.2 - 2022-02-13 =
- Fix fatal error on WP < 5

= 3.13.1 - 2022-02-10 =
- Fix noindex is added to robots with some plugins/themes alter the main query loop

= 3.13.0 - 2022-02-08 =
- Add integration for Bricks Builder
- Fix jumping when switching tabs
- Fix not rendering dynamic blocks for meta tags

= 3.12.0 - 2022-01-07 =
- Add Divi compatibility. Props Jay (@grandeljay)
- Disable HTTPS notification on local environments
- Improve the code of the settings page

= 3.11.1 - 2021-11-16 =
- Fix error on taxonomy page

= 3.11.0 - 2021-11-16 =
- Show meta title and meta description in admin columns
- Add migration for SEOPress
- Migrate noindex settings

= 3.10.2 - 2021-09-27 =
- Breadcrumbs: add link to post type archive for taxonomies if the taxonomy is attached to one post type only
- Fix text domain for string translations

= 3.10.1 - 2021-07-22 =
- Fix Jetpack Boost defer JS made sitemap disabled
- Fix Meta Box plugin shortcodes parsed for the meta description

= 3.10.0 - 2021-03-15 =
- Add fb:app_id and twitter:site meta tags
- Improve style for settings page
- Remove header cleaner service which breaks HTTPS checking in Site Health and provides no SEO benefits.
- Fix missing message when saving settings
- Fix cannot disable auto redirection

= 3.9.4 - 2021-03-06 =
- Improve meta robots tag to be compatible with WordPress 5.7

= 3.9.3 - 2021-03-01 =
- Add theme support for meta title in case theme authors forget
- Fix notice appear for article author in Open Graph
- Improve migration from AIO SEO

= 3.9.2 - 2021-02-08 =
- Improve migration from AIO SEO

= 3.9.1 - 2020-02-06 =
- Add filter for open graph tag values
- Add article:author open graph tag to support Pinterest

= 3.9.0 - 2020-12-24 =
- Add options to enable/disable plugin features

= 3.8.0 - 2020-12-02 =
- Add migration from Rank Math
- Fix rel links for the the static blog page
- Add filter for Open Graph tags

= 3.7.1 - 2020-11-18 =
- Hide Elementor, Beaver Builder, Oxygen template post types in sitemaps

= 3.7.0 - 2020-11-17 =
- Add integration for WPML and Polylang.

= 3.6.2 - 2020-11-02 =
- Fix canonical URL for static blog page.

= 3.6.1 - 2020-10-26 =
- Fix no spaces between HTML tags when generate description automatically in Oxygen
- Hide SEO settings meta box for Oxygen templates
- Fix PHP notice when generate meta description if post not found

= 3.6.0 - 2020-10-21 =
- Add default Facebook and Twitter images for sharing
- Don't remove meta values from other SEO plugins after migration (for safety)

= 3.5.4 - 2020-09-29 =
- Show large image in the SERP
- Fix SEOFramework migration

= 3.5.3 - 2020-09-10 =
- Add support for Velocity plugin.

= 3.5.2 - 2020-09-05 =
- Fix SEO settings not appear for WooCommerce categories.
- Add Settings link on the plugin row.

= 3.5.1 - 2020-08-10 =
- Hotfix for not checking Google Web Stories.

= 3.5.0 - 2020-08-10 =
- Add support for Google Web Stories.
- Improve integrations for Jetpack and AMP. Disable Jetpack SEO tools completely when the plugin is activated.
- Ensure no meta if users enter nothing.
- Sanitize plugin options, remove option if no settings is saved.

= 3.4.8 - 2020-08-06 =
- Temporarily disable the core sitemaps in WordPress 5.5 to avoid any conflict and redundancy. We're working on a deep integration to make sure Slim SEO works well with it.

= 3.4.7 - 2020-07-29 =
- Fix typo in JS which breaks the preview of meta fields.

= 3.4.6 - 2020-07-28 =
- Fix JS error can't update counter.
- Remove invalid characters in the sitemap XML.
- Fix non-protocol URL of images in the sitemap.

= 3.4.5 - 2020-07-21 =
- Fix image relative URLs in the sitemap.
- Add default title for images in the sitemap.
- Fix JavaScript error if the post edit is customized and don't have the plugin inputs.

= 3.4.4 - 2020-07-16 =
- Fix Oxygen's code blog not working.

= 3.4.3 - 2020-07-06 =
- Fix compatibility with EDD Invoices plugin.

= 3.4.2 - 2020-06-19 =
- Fix Oxygen slider not rendering properly.
- Increase the maximum number of URLs in sitemap to 2000.
- Add filter to change query args for sitemaps.
- Paginate taxonomy sitemap.

= 3.4.1 - 2020-06-12 =
- Hotfix error editing Oxygen templates.

= 3.4.0 - 2020-06-12 =
- Add integration for LifterLMS: fix compatibility issues and allow users to define meta title/description for catalog pages. Props Rocco Aliberti.
- Add integration for Oxygen builder, now the plugin parses the content built with Oxygen.
- Add filter for changing context/priority for meta box.
- Add support for output code after the opening `<body>` tag.
- Fix illegal string offset ‘url’ in post sitemap.
- Use WP native `.description` CSS class.

= 3.3.2 - 2020-05-21 =
- Add password protection support for meta description
- Add rel prev/next links for archive pages

= 3.3.1 - 2020-05-04 =
- Fix missing function get_term_value for image

= 3.3.0 - 2020-05-03 =
- Add canonical URL for missing pages
- Add settings for homepage when it shows latest posts
- Add filter for breadcrumb links to make work with other plugins
- Improve UI, removing tabs

= 3.2.3 - 2020-04-20 =
- Remove canonical link when a page is not indexed
- Fix "Hide from search result pages" not working for static blog & WooCommerce shop pages.
- Fix wrong text domain

= 3.2.2 - 2020-02-25 =
- Fix loopback request failed in Site Health
- Load Open Graph, Twitter Cards & Breadcrumbs on the front end only
- Update JavaScript code, using vanilla JavaScript (no jQuery)

= 3.2.1 - 2020-02-12 =
- Fix stable tag in readme

= 3.2.0 - 2020-02-12 =
- Add migration tool to migrate SEO data from popular SEO plugins.

= 3.1.3 =
- Fixed: Fix SEO settings not working for WooCommerce shop
- Fixed: Fix multiple messages sent when using Very Simple Contact Form
- Fixed: Fix WordPress deprecation notice for `sanitize_url`
- Fixed: Fix "non-object" error on posts having no author
- Changed: Remove filter to disable schema. Use `slim_seo_init` hook instead.

= 3.1.2 =
- Fixed: Add missing meta description for author archive
- Changed: Allow developers to disable features

= 3.1.1 =
- Fixed: Fix JS error when the admin notice is hid by other plugins
- Fixed: Fix <link> tag from HappyForms being removed
- Changed: Remove noindexed posts/terms from sitemap

= 3.1.0 =
- Add image settings for social networks

= 3.0.2 =
- Add live preview for meta tags

= 3.0.1 =
- Added: Add character counter for custom meta tags
- Changed: Change length of meta description to 160 characters.
- Fixed: Fix custom meta tags for terms

= 3.0.0 =
- Add meta box for entering custom meta tags for posts and terms
- Add settings page for entering header/footer code (Google Analytics, Google Tag Manager, webmaster tools verification)

= 2.1.1 =
- Output paginated sitemap for sites with thousands of posts

= 2.1.0 =
- Add support for AMP
- Add image sitemap
- Add Organization schema

= 2.0.3 =
- Fixed: Fix no error notice on WooCommerce login page.
- Added: Add SSL warning.

= 2.0.2 =
- Disable schema markup for Beaver Theme and Genesis.

= 2.0.1 =
- Output Article and Author for single posts only.

= 2.0.0 =
- Improved Schema markup. Use united schema output inside one `<script>` tag and add connections between entities. See [documentation](https://docs.wpslimseo.com/slim-seo/schema/) for details.

= 1.5.1 =
- Removed preferred site name from JSON-LD as Google drops support for it.
- Made notification dismissible.

= 1.5.0 =
- Added: Added excerpt to pages to let users customize meta description, especially for static homepage.
- Added: Added support for [JSON-LD structured data](https://github.com/elightup/slim-seo/wiki/JSON-LD). Supported Website data (including search box) and Breadcrumbs.
- Added: [Redirect author page](https://github.com/elightup/slim-seo/wiki/Auto-Redirection) to homepage if no posts or the website has only one user.
- Added: Added support for [meta robots tag](https://github.com/elightup/slim-seo/wiki/Meta-Robots-Tag).
	- Do not index the follow links:
		- search results
		- 404
		- feed
		- private posts
		- page with no content
	- Do not follow links:
		- register
		- login

= 1.4.0 =
- Added: Added notification for SEO settings errors in the admin.
- Added: Added link to original post in the feed.
- Changed: Flushed rewrite rules on plugin activate/deactivate to make Sitemap URL works automatically.
- Fixed: Fixed shortcodes in post content not parsed in meta description. Fixed for page builders like Divi.

= 1.3.1 =
- Changed: Redirect attachment page to the attachment URL (image or file URL) instead of parent post. This allows users to see the full-size image. Works when users insert a gallery into post and they want to see full-size images.

= 1.3.0 =
- Added: Added breadcrumbs functionality. Use `[slim_seo_breadcrumbs]` shortcode to output in your template files. See [documentation](https://github.com/elightup/slim-seo/wiki/Breadcrumbs).

= 1.2.0 =
- Added: Added sitemap URL to robots.txt
- Added: Auto add missing alt attribute for images.

= 1.1.0 =
- Added: redirect attachment page to parent post.

= 1.0 =
- First version.
