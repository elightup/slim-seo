=== Slim SEO - Fast & Automated WordPress SEO Plugin ===
Contributors: elightup, rilwis, hungviet91, barcavn2
Donate link: https://wpslimseo.com/products/
Tags: seo, schema, xml sitemap, redirection, header
Requires at least: 5.9
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 4.5.2
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A full-featured SEO plugin for WordPress that's lightweight, blazing fast with minimum configuration. No bloats and just works!

== Description ==

### A Fast & Automated SEO Plugin For WordPress

Currently there are many SEO plugins for WordPress in the market. But these plugins often have too many options and are very complicated for ordinary users. Access to their configuration section, you will easily get lost in a maze of explanations and options that you sometimes don't understand. Besides, there are ads!

**So how can an ordinary user use an SEO plugin?**

SEO should be an integrated part of WordPress, where users don't need or need very little effort to configure for SEO. The main reason is that not everyone understands the terms of SEO and how to configure them optimally.

So, we made [**Slim SEO**](https://wpslimseo.com).

https://www.youtube.com/watch?v=MVNjGAiu2bg

https://www.youtube.com/watch?v=vnC94TMn3wU

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
- LinkedIn meta tags

#### 2. [XML Sitemap](https://docs.wpslimseo.com/slim-seo/xml-sitemap/)

Slim SEO automatically generates XML sitemap (at `domain.com/sitemap.xml`) to submit to search engines. With XML sitemaps, your website are indexed fast and completely.

Besides the normal XML sitemap, Slim SEO also includes sitemaps for images and Google news.

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

### Slim SEO Pro

Upgrade to [Slim SEO Pro](https://elu.to/wrp) to have access to advanced SEO features without complexity:

- Visual schema builder
- 30+ pre-built schema types
- Custom schema with JSON-LD
- Contextual link suggestions
- Real-time link health monitoring
- Broken link repair
- Link updater

[Get Slim SEO Pro now](https://elu.to/wrp).

### Who should use Slim SEO?

Everyone can use Slim SEO!

However, Slim SEO is perfectly suitable for users who prefer simplicity or do not like the complicated options that other SEO plugins provide. It's also a good choice for users with little SEO knowledge and just want to use SEO plugins to automate their jobs.

## You might also like

If you like this plugin, you might also like our other WordPress products:

- [Meta Box](https://metabox.io) - A powerful WordPress plugin for creating custom post types and custom fields.
- [GretaThemes](https://gretathemes.com) - Free and premium WordPress themes that clean, simple and just work.
- [Auto Listings](https://wpautolistings.com) - A car sale and dealership plugin for WordPress.

== Installation ==

Before installing, please note that the plugin requires PHP >= 7.2.

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

= How to remove plugin's data when uninstalling =

Add the following constant in your `wp-config.php` file:

`define( 'SLIM_SEO_DELETE_DATA', true );`

== Screenshots ==

== Upgrade Notice ==

== Changelog ==

= 4.5.2 - 2025-04-19 =
- Fix Bricks compatibility when using with nested loops
- Ignore Divi's `layout_category` post type

= 4.5.1 - 2025-04-11 =

- To avoid issues with Bricks, from now the plugin doesn't parse the content from Bricks for generating meta tags if the post uses a template. In this case the plugin always use the post content.
- Fix rendering meta tags in the admin post list table as Bricks doesn't support parsing dynamic tags in the admin.
- Ignore Bricks components when parsing meta tags
- Fix core/heading block is excluded from parsing for meta tags

= 4.5.0 - 2025-03-27 =

Highlights:

- This version improves the integration for Polylang and WPML, allowing you to switch languages for the settings page and enter different settings per language.
- Add support for Visual Composer (WPBakery Page Builder) and Tagdiv Composer (Newspaper theme)

Other changes:
- Exclude Beaver Builder shortcode by default
- Do not enqueue JS for non-supported post types in the admin
- Fix the integration for Sensei LMS
- Fix issues with WooCommerce integration to not parsing content of pages below cart/checkout/my account pages in admin page table list

= 4.4.1 - 2025-03-04 =
- Improve Woo integration, avoid page builders to parse content for Woo's pages to generate meta description (cart, checkout, my account)
- Fix for JetEngine custom DB table for meta storage
- Fix wrong og:url

= 4.4.0 - 2025-03-03 =

Highlights:

- This version adds an integration with Breakdance page builder plugin, which allows the plugin to parse content from Breakdance for generating meta description.
- Since this version, Slim SEO won't parse all dynamic blocks when parsing the content to generate meta description. This avoids breaking layout, especially when those blocks have logic or enqueue CSS/JS. This change will fix a lot of issues with plugins that use dynamic blocks like form plugins (JetForm Builder, Forminator, etc.).
- For meta title and meta description in the admin post list table, now the plugin will display a small indicator if the post has manual title or description.

Other changes:

- Improve integration with WPML, allowing to translate settings like meta tags into different languages. Requires WPML String Translation plugin.
- Capitalize only 1st letter in image alt to work better with all languages.
- Fix missing posts in alternate languages in the sitemap that don't have translations in the default language (with Polylang).
- Fix missing separator in title for paginated pages
- Fix issue with REST API

= 4.3.0 - 2025-02-03 =
- Always show the meta tags in admin post table columns because the settings for post types with dynamic variables can be different from the WordPress default. Previously, the plugin only show meta tags if the post has custom settings.
- Improve the Rest API support, which now returns parsed meta tags with dynamic variables
- Add the integration for Kadence product family
- Improve the schema structure
- Improve the compatibility with Bricks, excluding image and gallery elements
- Improve the breadcrumbs for WooCommerce's search results page
- Fix breadcrumbs issue which causes errors for SureCart or Elementor

= 4.2.2 - 2025-01-20 =
- Improve schemas
- Move meta tags to the top of <head> section
- Add current month variable
- Not index comment pages
- Fix wrong taxonomy for non-product page when WooCommerce is active
- Fix compatibility issue with Dokan
- Fix bug with bulk edit

= 4.2.1 - 2024-12-16 =
- Show post type & taxonomy slugs in the Meta Tags tab to avoid confusion if 2 post types/taxonomies have same labels.
- Improve compatibility for WPForo
- Fix compatibility with old versions of Meta Box
- Fix reading post_type on null – Image.php

= 4.2.0 - 2024-12-03 =

Highlights: this version adds a new feature that allows you to remove `/category/` base from the category permalink.

Other changes:
- Add support for ACF and WooCommerce fields from the dynamic variables for meta tags
- Allow you to select a post/page as the destination when creating a redirect
- Add sample data for importing redirections
- Fix conflicts with GT3 themes

= 4.1.1 - 2024-11-17 =
- Fix breadcrumbs issue

= 4.1.0 - 2024-11-16 =
- Support [Meta Box](https://metabox.io)'s custom fields for dynamic variables, including groups and settings pages
- Re-add options to add "rel" attribute (nofollow, ugc, sponsored) to links in the block editor
- Add integration with wpForo plugin
- Add filters for toggle news & image sitemap
- Fix: 404 logs pagination error
- Fix languages loaded too early in WordPress 6.7
- Fix zoom feature in WP 6.7 not working because the breadcrumb block uses API version 2
- Fix not excluding taxonomies issue from the sitemap when enable the option "Hide from search engines" in the Meta Tags settings page
- Fix PHP warning when getting all post metas

= 4.0.4 - 2024-11-05 =
- Improve migration from other SEO plugins: migrate dynamic variables instead of their values
- Add filter `slim_seo_twitter_card_type` for twitter card type
- Fix quick/bulk edit not working

= 4.0.2 - 2024-10-17 =
- Fix Divi and WooCommerce compatibility
- Fix empty Meta Tags tab on WordPress < 6.6

= 4.0.2 - 2024-10-15 =
- Fix missing style for MetaTags tab
- Improve compatibility with page builders

= 4.0.1 - 2024-10-12 =
- Add integration for Forminator
- Bricks: Ignore elements with scripts, like sliders or counters, to avoid breaking layouts.
- Fix static blog page returning wrong meta
- Fix: Redirects list not update after create/update/delete redirect because of Litespeed cache
- Fix: Surecart product bug

= 4.0.0 - 2024-10-08 =

**Highlights:**

This version allows you to configure meta tags with dynamic variables, whose value can be changed according to the content or data of the article, website, or in real time. You can set up meta tags more flexibly for homepage, all post types and taxonomies.

Read more about this in our [blog post](https://wpslimseo.com/slim-seo-v4-configuring-meta-tags-with-dynamic-variables/).

Other changes:

- Improve integrations for page builder plugins, removing internal post types and taxonomies from them
- Do not expose username in the Person schema for a security reason
- Improve preview for breadcrumbs block
- Prevent direct access to PHP files
- Remove link attributes (ability to add nofollow to links) for the block editor as the block editor already supports it
- Fix non-integer custom logo value breaks schemas in Blocksy theme

For full changelog, see [here](https://github.com/elightup/slim-seo/blob/master/CHANGELOG.md).