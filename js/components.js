/*
## Purpose

Global JS components that are used across Slim SEO free and pro pages.
- Admin settings page: Settings > Slim SEO
- Edit post: All posts > Add new/Edit post

## Components

Only general, multi-purpose components are included here:

- Tabs: used for settings page and SEO meta box when any premium plugin is active

## How to use

In all plugins, enqueue the following file:

```php
wp_enqueue_script( 'slim-seo-components', 'https://cdn.jsdelivr.net/gh/elightup/slim-seo@master/js/components.js', [], '1.0.0', true );
```

Replace version with the latest version of the plugin.
*/

( function( document, location ) {
	const tabs = document.querySelectorAll( '.ss-tab' ),
		panes = document.querySelectorAll( '.ss-tab-pane' );

	function clickHandle( e ) {
		if ( !e.target.classList.contains( 'ss-tab' ) ) {
			return;
		}

		e.preventDefault();

		history.pushState( null, null, e.target.getAttribute( 'href' ) );

		tabs.forEach( tab => tab.classList.remove( 'ss-is-active' ) );
		e.target.classList.add( 'ss-is-active' );

		panes.forEach( pane => pane.classList.remove( 'ss-is-active' ) );
		document.querySelector( e.target.getAttribute( 'href' ) ).classList.add( 'ss-is-active' );
	}

	function activateTab( tab ) {
		const tabElement = document.querySelector( `a[href="${ tab }"]` );
		const paneElement = document.querySelector( tab );

		if ( !tabElement || !paneElement ) {
			return false;
		}

		tabElement.classList.add( 'ss-is-active' );
		paneElement.classList.add( 'ss-is-active' );

		return true;
	}

	function activateDefaultTab() {
		let hash = location.hash;
		let result = false;

		if ( hash ) {
			result = activateTab( hash );
		}

		if ( !result ) {
			hash = tabs[ 0 ].getAttribute( 'href' );
			activateTab( hash );
		}
	}

	document.querySelector( '.ss-tab-list' ).addEventListener( 'click', clickHandle );

	activateDefaultTab();
} )( document, location );
