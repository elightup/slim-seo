=== Slim SEO - Fast & Automated WordPress SEO Plugin ===
Contributors: elightup, rilwis
Donate link: https://paypal.me/anhtnt
Tags: seo, search engine optimization, google, facebook, twitter, meta tags, meta description, open graph, twitter card, sitemap, xml sitemap
Requires at least: 4.5
Tested up to: 5.3.2
Requires PHP: 5.6
Stable tag: 3.1.2
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A full-featured SEO plugin for WordPress that's lightweight, blazing fast with minimum configuration. No bloats, no ads and just works!

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

- [Meta title tag](https://wpslimseo.com/docs/meta-title-tag/): display your title in a SEO-friendly way.
- [Meta description tag](https://wpslimseo.com/docs/meta-description-tag/): auto generate from posts/pages excerpt or content.
- [Meta robots tag](https://wpslimseo.com/docs/meta-robots-tag/): decide which pages are indexed and which ones not.
- [Facebook Open Graph Tags](https://wpslimseo.com/docs/facebook-open-graph-tags/): share your posts on Facebook beautifully.
- [Twitter Card Tags](https://wpslimseo.com/docs/twitter-card-tags/): share your posts on Twitter beautifully.

#### 2. [XML Sitemap](https://wpslimseo.com/docs/xml-sitemap/)

Slim SEO automatically generates XML sitemap (at `domain.com/sitemap.xml`) to submit to search engines. With XML sitemaps, your website are indexed fast and completely.

#### 3. [Breadcrumbs](https://wpslimseo.com/docs/breadcrumbs/)

The plugin allows you to output a breadcrumb trail on your website easily. It automatically fetches the information from the current post and output a hierarchy for you. You can also style the breadcrumbs to match your theme style.

#### 4. [Schema (Structured Data)](https://wpslimseo.com/docs/schema/)

Schema is a way that describes structured data for search engines. Based on the data provided, search engines can show the content in the search results page in a more appealing way.

Slim SEO automatically adds the some structured data to the website via JSON-LD which makes your website more SEO-friendly. Not only schemas are created by the plugin, there are also meaningful connections between them. For example, an article (single post) is the main entity of the current webpage. Slim SEO does that all without any configuration.

#### 5. [Auto Redirection](https://wpslimseo.com/docs/auto-redirection/)

- Auto redirect attachment page to the attachment file URL.
- Auto redirect author page to the homepage if the website has only one author or the author doesn't have any posts.

To know more about Slim SEO features, please see the [homepage](https://wpslimseo.com).

### Who should use Slim SEO?

Everyone can use Slim SEO!

However, Slim SEO is perfecly suitable for users who prefer simplicity or do not like the complicated options that other SEO plugins provide. It's also a good choice for users with little SEO knowledge and just want to use SEO plugins to automate their jobs.

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

It's not available at the moment. Our purpose is provide a configuration-free SEO plugin for WordPress, so you don't get lost in the options. We might improve the plugin later to add essential options. But for now, it's zero configuration.

If you need custom settings, we recommend using Yoast SEO plugin.

= Where is my XML sitemap? =

The sitemap URL is located at `domain.com/sitemap.xml`.

== Screenshots ==

== Upgrade Notice ==

== Changelog ==

= 3.1.2 =
- Fixed: Add missing meta description for author archive
- Changed: Allow developers to disable features

= 3.1.1 =
- Fixed: Fix JS error when the admin notice is hid by other plugins
- Fixed: Fix <link> tag from HappyForms being removed
- Changed: Remove noindexed posts/terms from sitemap

= 3.1.0 =
- Added: Add image settings for social networks

= 3.0.2 =
- Added: Add live preview for meta tags

= 3.0.1 =
- Added: Add character counter for custom meta tags
- Changed: Change length of meta description to 160 characters.
- Fixed: Fix custom meta tags for terms

= 3.0.0 =
- Added: Add meta box for entering custom meta tags for posts and terms
- Added: Add settings page for entering header/footer code (Google Analytics, Google Tag Manager, webmaster tools verification)

= 2.1.1 =
- Changed: Output paginated sitemap for sites with thousands of posts

= 2.1.0 =
- Added: Add support for AMP
- Added: Add image sitemap
- Added: Add Organization schema

= 2.0.3 =
- Fixed: Fix no error notice on WooCommerce login page.
- Added: Add SSL warning.

= 2.0.2 =
- Improved: Disable schema markup for Beaver Theme and Genesis.

= 2.0.1 =
- Fixed: Output Article and Author for single posts only.

= 2.0.0 =
- Improved: Improved Schema markup. Use united schema output inside one `<script>` tag and add connections between entities. See [documentation](https://wpslimseo.com/docs/schema/) for details.

= 1.5.1 =
- Changed: Removed preferred site name from JSON-LD as Google drops support for it.
- Changed: Made notification dismissible.

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
