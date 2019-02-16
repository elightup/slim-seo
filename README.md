[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/elightup/slim-seo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/elightup/slim-seo/?branch=master)
[![Version](https://img.shields.io/wordpress/plugin/v/slim-seo.svg)](http://wordpress.org/plugins/slim-seo/)
[![WordPress Version](https://img.shields.io/wordpress/v/slim-seo.svg)](http://wordpress.org/plugins/slim-seo/)

# Slim SEO - A Fast & Automated SEO Plugin For WordPress

Currently there are many SEO plugins for WordPress in the market. But these plugins often have too many options and are very complicated for ordinary users. Access to their configuration section, you will easily get lost in a maze of explanations and options that you sometimes don't understand. Besides, there are ads!

**So how can an ordinary user use an SEO plugin?**

At [eLightUp](https://elightup.com), we believe that SEO should be an integrated part of WordPress, where users don't need or need very little effort to configure for SEO. The main reason is that not everyone understands the terms of SEO and how to configure them optimally.

So, we made [**Slim SEO**](https://elightup.com/products/slim-seo/).

**Slim SEO** follow the philosophy of WordPress, decision over option. That means we provide an SEO plugin for WordPress where the configuration has been done automatically. Users do not need to care about their complex and semantic options.

So what does **Slim SEO** do?

## Slim SEO Features

[**Slim SEO**](https://elightup.com/products/slim-seo/) helps you do the following jobs automatically:

### 1. Optimize meta tags

The meta title and meta description tag is optimized and displayed automatically.

You don't need to think about which character to use to separate the title parts, or where to put the site title (after or before the page title).

The meta description tag is automatically generated from the post excerpt or post content (in case you didn't enter post excerpt). For categories, post tags or custom taxonomies, their description will be used as meta description.

All of these operations are automatically done by the **Slim SEO** plugin. And you don't need to configure anything.

### 2. Optimize Open Graph and Twitter Cards tags

**Slim SEO** automatically optimizes and displays Open Graph and Twitter Cards meta tags for your posts. Thus your posts will be displayed beautifully when sharing on Facebook and Twitter.

When sharing posts on Twitter, **Slim SEO** automatically optimizes the article to be displayed in a large card form, a beautiful and highly interactive style.

### 3. Create XML Sitemap

**Slim SEO** automatically generates XML sitemap (at `domain.com/sitemap.xml`) to submit to search engines. With XML sitemaps, your website are indexed fast and completely.

### 4. Redirect attachment page to the attachment file URL

When you upload any file to WordPress, WordPress creates an attachment page for that file. This page provides very little information about the file and the page looks quite empty. As search engines already index the files, allowing attachment pages to be indexed make duplicated content and increase the bounce rate. It's better to disable the attachment pages.

Without any configuration from users, **Slim SEO** will help you to disable the attachment pages automatically and redirects them to the file URL. Users will see the real file and thus, no empty pages.

### 5. Auto add missing alt attribute for images

The plugin automatically adds missing alt attribute for images, including post thumbnails and images inserted in the post content.

### 6. Breadcrumbs

The plugin provides `[slim_seo_breadcrumbs]` shortcode that allows you to output a breadcrumb trail on your website easily. The plugin uses schema.org syntax which is compatible with Google Structured Data recommendation. See [documentation](https://github.com/elightup/slim-seo/wiki/Breadcrumbs) for details.

### 7. Prevent content stealing from RSS feed

With **Slim SEO**, all posts in your RSS feed have a backlink to your original posts. So if someone tries to steal your content from RSS feed, they'll link back to your post. Thus, Google and other search engines will understand which post is original and then increase your SEO ranking.

## Who should use Slim SEO

Everyone can use **Slim SEO**.

However, **Slim SEO** is perfecly suitable for users who prefer simplicity or do not like the complicated options that other SEO plugins provide. It's also a good choice for users with little SEO knowledge and just want to use SEO plugins to automate their jobs.

## Installation

1. Go to Plugins > Add New.
2. Search for "Slim SEO".
3. Install and activate the plugin.

That's all. The plugin doesn't have any settings page. Everything is done automatically.

## Frequently Asked Questions

**How meta title tag is generated?**

WordPress already has this featured! All we need to do is add theme support for `title-tag`. By default, the title tag will have the following format:

For homepage: Site title - Site description
For single page or post: Page/Post title - Site title
For other pages: Page title - Site title

This format is pretty good!

**How meta description tag is generated?**

The meta description tag is automatically generated from the post excerpt or post content (in case you didn't enter post excerpt). For categories, post tags or custom taxonomies, their description will be used as meta description.

**How Open Graph meta tags is generated?**

Open Graph inherits the meta title and meta description. For image tag, it gets from the featured image.

For single posts, the plugin also provides detailed information for article section (the first post category) and article tags (post tags). It also provides additional information such as published time and modified time.

Other tags such as `url`, `site_name`, `type`, `locale` are quite obvious.

**How Twitter card meta tags is generated?**

Twitter inherits some tags from Open Graph such as title, description and image. So we only need to set the card type to large image, so the posts appear beautifully on Twitter.

**How do I set meta tags for my homepage?**

If your homepage is a static page, then it's treated like a normal page. The meta description is generated from page content, and the featured image will be used for Open Graph tags.

If your homepage shows latest posts, then it's already done automatically by the plugin.

**Can I change the meta tags manually?**

It's not available at the moment. Our purpose is provide a configuration-free SEO plugin for WordPress, so you don't get lost in the options. We might improve the plugin later to add essential options. But for now, it's zero configuration.

If you need custom settings, we recommend using Yoast SEO plugin.

**Where is my XML sitemap?**

The sitemap URL is located at `domain.com/sitemap.xml`.
