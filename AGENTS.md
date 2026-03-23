# AGENTS.md - Slim SEO Plugin

Guidelines for AI agents working on this codebase.

## Overview

This is a WordPress SEO plugin (Slim SEO) written in PHP with React/JavaScript frontend. The project uses PSR-4 autoloading with namespace `SlimSEO\`.

## Build Commands

### PHP (Composer)
```bash
# Lint check
composer phpcs src

# Auto-fix linting issues
composer phpcbf src

# Static analysis
composer phpstan src

# Run a single PHP file through phpcs
composer phpcs path/to/file.php

# Run a single PHP file through phpstan
composer phpstan analyse path/to/file.php
```

### JavaScript/Node (npm)
```bash
# Build all assets (CSS + JS)
pnpm run build

# Build CSS only
pnpm run build:css

# Build JS only
pnpm run build:js

# Watch CSS for changes during development
pnpm run css:watch

# Start dev server (Webpack)
pnpm start
```

## Code Style Guidelines

### General
- **Minimum PHP version**: 7.2
- **Minimum WordPress version**: 6.5
- **Coding standards**: WordPress Coding Standards (via phpcs.xml)
- **Text domain**: `slim-seo` (for i18n)

### Naming Conventions
- **Classes**: PascalCase (e.g., `class Core`, `class MetaTagsHelper`)
- **Files**: Match class name (e.g., `Core.php`, `MetaTags/Helper.php`)
- **Namespaces**: `SlimSEO\[ModuleName]`
- **Functions/methods**: snake_case
- **Constants**: UPPER_CASE
- **Hook callbacks**: Prefix with hook name `slim_seo` (e.g., `slim_seo_meta_title`)

### PHP Standards
- **Arrays**: Short syntax `[]` allowed
- **Echo**: Short echo `<?= ?>` allowed
- **Ternary**: Short ternary allowed
- **Globals prefix**: `slim_seo` or `SlimSEO`
- **i18n**: Use `__( 'Text', 'slim-seo' )` and `_x( 'Text', 'Context', 'slim-seo' )`

### Imports
```php
use SlimSEO\Helpers\Data;
use SlimSEO\Schema\Types\Article;
```

### Type Hints
Use PHP type hints for function parameters and return types:
```php
public function get_posts( array $args = [] ): array {
    // ...
}
```

### Class Structure
```php
<?php
namespace SlimSEO\ModuleName;

class ClassName {
    public function setup(): void {
        // Hook registration in setup() method
    }

    public function method_name(): void {
        // ...
    }
}
```

### Error Handling
- Use WordPress functions (`wp_die()`, `add_action()` with priority)
- Return early for error conditions
- Check for null/false with proper conditionals

## Architecture

### Directory Structure
```
src/
├── Core.php              # Main plugin class
├── Helpers/              # Helper classes
├── MetaTags/              # Meta tags generation
├── Schema/               # Schema.org structured data
├── Sitemaps/             # XML sitemaps
├── Migration/             # Data migration from other plugins
└── Integrations/          # Third-party plugin integrations
```

### Key Classes
- `SlimSEO\Core`: Main plugin setup
- `SlimSEO\MetaTags\*`: Meta tags (title, description, OpenGraph, etc.)
- `SlimSEO\Schema\Manager`: Schema.org data
- `SlimSEO\Sitemaps\*`: XML sitemap generation

## Dependencies

### External Libraries
- `elightup/slim-twig`: Twig templating
- `elightup/slim-seo-common`: Common helpers
- `@elightup/form`: React form library
- React ecosystem: react-paginate, react-select, react-tabs, swr

## Testing

No formal test suite exists. For manual testing:
1. Build assets: `pnpm run build`
2. Lint PHP: `composer phpcs`
3. Run static analysis: `composer phpstan`

## Important Notes

- WordPress plugin with admin settings pages
- Uses Twig templates (from slim-twig package)
- Auto-migrates data from: Yoast, All In One SEO, The SEO Framework, Rank Math, SEOPress, Squirrly SEO, Redirection, 301 Redirects
- Integrates with many page builders (Elementor, Divi, Beaver Builder, etc.)
