# Developer Hook Examples

Slim SEO exposes WordPress filters and actions for developers who need to customize SEO output without editing plugin files.

These examples can be added to a small custom plugin, a child theme `functions.php` file, or a code snippets plugin. Use conditions so each snippet only runs where it is needed, and always return the original value when your condition does not apply.

## Meta Tags

### Customize the Meta Title

Use the `slim_seo_meta_title` filter to adjust the generated meta title.

```php
add_filter( 'slim_seo_meta_title', function ( string $title, int $object_id ): string {
	if ( is_singular( 'product' ) ) {
		return $title . ' | Product Catalog';
	}

	return $title;
}, 10, 2 );
```

### Customize the Meta Description

Use the `slim_seo_meta_description` filter to adjust the generated meta description.

```php
add_filter( 'slim_seo_meta_description', function ( string $description, int $object_id ): string {
	if ( is_singular( 'product' ) && has_excerpt( $object_id ) ) {
		return get_the_excerpt( $object_id );
	}

	return $description;
}, 10, 2 );
```

### Customize the Canonical URL

Use the `slim_seo_canonical_url` filter to adjust the generated canonical URL.

```php
add_filter( 'slim_seo_canonical_url', function ( string $url, int $object_id ): string {
	if ( is_singular( 'product' ) ) {
		$canonical = get_post_meta( $object_id, '_custom_canonical_url', true );

		if ( $canonical ) {
			return esc_url_raw( $canonical );
		}
	}

	return $url;
}, 10, 2 );
```

## Schema

### Modify the Schema Graph

Use the `slim_seo_schema_graph` filter to modify JSON-LD graph entities.

```php
add_filter( 'slim_seo_schema_graph', function ( array $graph ): array {
	foreach ( $graph as &$entity ) {
		if ( ( $entity['@type'] ?? '' ) === 'WebPage' && is_singular( 'product' ) ) {
			$entity['mainEntity'] = [
				'@type' => 'Product',
				'name'  => get_the_title(),
			];
		}
	}
	unset( $entity );

	return $graph;
} );
```

### Disable a Schema Type

Each built-in schema type can be disabled with a context-specific filter. For example, disable the `Article` schema on single posts:

```php
add_filter( 'slim_seo_schema_article_enable', function ( bool $enabled ): bool {
	if ( is_singular( 'post' ) ) {
		return false;
	}

	return $enabled;
} );
```

## Sitemaps

### Exclude Posts from Post Type Sitemaps

Use the `slim_seo_sitemap_post_ignore` filter to skip specific posts in post type sitemaps.

```php
add_filter( 'slim_seo_sitemap_post_ignore', function ( bool $ignore, WP_Post $post ): bool {
	if ( has_term( 'internal', 'category', $post ) ) {
		return true;
	}

	return $ignore;
}, 10, 2 );
```

### Adjust Post Type Sitemap Query Arguments

Use the `slim_seo_sitemap_post_type_query_args` filter to adjust the `WP_Query` arguments used for post type sitemaps.

```php
add_filter( 'slim_seo_sitemap_post_type_query_args', function ( array $query_args, array $input_args ): array {
	if ( ( $input_args['post_type'] ?? '' ) === 'product' ) {
		$query_args['posts_per_page'] = 500;
		$query_args['meta_query']     = [
			[
				'key'     => '_stock_status',
				'value'   => 'instock',
				'compare' => '=',
			],
		];
	}

	return $query_args;
}, 10, 2 );
```

### Disable Image Sitemaps

Use the `slim_seo_sitemap_image` filter to disable image entries in post type sitemaps.

```php
add_filter( 'slim_seo_sitemap_image', '__return_false' );
```

### Disable News Sitemaps

Use the `slim_seo_sitemap_news` filter to disable news sitemap entries.

```php
add_filter( 'slim_seo_sitemap_news', '__return_false' );
```

### Enable the User Sitemap

The user sitemap is disabled by default. Use the `slim_seo_user_sitemap` filter to enable it when author archive URLs should be included.

```php
add_filter( 'slim_seo_user_sitemap', '__return_true' );
```

You can also adjust the user query arguments:

```php
add_filter( 'slim_seo_user_query_args', function ( array $query_args, array $input_args ): array {
	$query_args['role__in'] = [ 'author', 'editor' ];

	return $query_args;
}, 10, 2 );
```

## Robots.txt

### Customize Robots.txt Output

Use the `slim_seo_robots_txt` filter to adjust the generated `robots.txt` content.

```php
add_filter( 'slim_seo_robots_txt', function ( string $content ): string {
	$content .= "\nDisallow: /private/\n";

	return $content;
} );
```

## Notes

- Do not edit Slim SEO plugin files directly. Use hooks from a child theme, custom plugin, or code snippets plugin.
- Keep conditions narrow so a customization only affects the intended content type or page.
- Test meta tags in the page source, schema output in the `slim-seo-schema` JSON-LD script, and sitemap changes in the relevant XML sitemap URL.
